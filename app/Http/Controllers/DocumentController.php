<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $query = Document::with('documentDetail', 'user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('documentDetail', function ($q) use ($search) {
                $q->where('nomor_dokumen', 'like', "%$search%")
                  ->orWhere('nama_pelanggan', 'like', "%$search%")
                  ->orWhere('sid', 'like', "%$search%");
            })->orWhere('file_name', 'like', "%$search%");
        }

        if ($request->filled('type') && $request->type != 'all') {
            $query->whereHas('documentDetail', function ($q) use ($request) {
                $q->where('tipe_dokumen', $request->type);
            });
        }

        $documents = $query->orderBy('upload_timestamp', 'desc')->paginate(10);

        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        if (!in_array(Auth::user()->role, ['admin', 'inputter'])) {
            abort(403);
        }

        $jenisLayananOptions = ['IP Transit', 'Metro E', 'CNDC', 'SL WDM', 'SCC', 'Digital'];
        $tipeOrderOptions = ['SO', 'DO', 'MO', 'RO'];
        $tipeDokumenOptions = ['Berita Acara', 'BAK', 'BA', 'PKS', 'PO', 'Other Document'];

        return view('documents.create', compact('jenisLayananOptions', 'tipeOrderOptions', 'tipeDokumenOptions'));
    }

    public function store(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'inputter'])) {
            abort(403);
        }

        $request->validate([
            'tipe_dokumen' => 'required|string|max:255',
            'nomor_dokumen' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'nama_pelanggan' => 'nullable|string|max:255',
            'lokasi_kerja' => 'nullable|string|max:255',
            'jenis_layanan' => 'nullable|in:IP Transit,Metro E,CNDC,SL WDM,SCC,Digital',
            'tipe_order' => 'nullable|in:SO,DO,MO,RO',
            'sid' => 'nullable|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
            'custom_file_name' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $fileName = $request->filled('custom_file_name')
            ? $request->custom_file_name . '.' . $file->getClientOriginalExtension()
            : $file->getClientOriginalName();

        $filePath = $file->storeAs('documents', $fileName, 'public');

        $document = Document::create([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'uploaded_by' => Auth::id(),
            'upload_timestamp' => now(),
            'slug' => null,
        ]);

        $documentDetail = DocumentDetail::create([
            'document_id' => $document->id,
            'tipe_dokumen' => $request->tipe_dokumen,
            'nomor_dokumen' => $request->nomor_dokumen,
            'bak' => $request->has('bak'),
            'ba' => $request->has('ba'),
            'pks' => $request->has('pks'),
            'po' => $request->has('po'),
            'description' => $request->description,
            'nama_pelanggan' => $request->nama_pelanggan,
            'lokasi_kerja' => $request->lokasi_kerja,
            'jenis_layanan' => $request->jenis_layanan,
            'tipe_order' => $request->tipe_order,
            'sid' => $request->sid,
            'tanggal_mulai' => $request->tanggal_mulai,
        ]);

        // Buat slug dari nama_pelanggan + nomor dokumen + tipe
        $subject = $request->nama_pelanggan ?? $request->nomor_dokumen ?? 'document';
        $slug = Str::slug($subject);
        $count = Document::where('slug', 'like', "$slug%")->count();
        $document->slug = $count ? "{$slug}-{$count}" : $slug;
        $document->save();

        return redirect()->route('documents.show', $document->slug)->with('success', 'Dokumen berhasil diunggah!');
    }

    public function show($slug)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $document = Document::with('documentDetail', 'user')->where('slug', $slug)->firstOrFail();
        return view('documents.show', compact('document'));
    }

    public function edit($slug)
    {
        if (!in_array(Auth::user()->role, ['admin', 'inputter'])) {
            abort(403);
        }

        $document = Document::with('documentDetail', 'user')->where('slug', $slug)->firstOrFail();

        $jenisLayananOptions = ['IP Transit', 'Metro E', 'CNDC', 'SL WDM', 'SCC', 'Digital'];
        $tipeOrderOptions = ['SO', 'DO', 'MO', 'RO'];
        $tipeDokumenOptions = ['Berita Acara', 'BAK', 'BA', 'PKS', 'PO', 'Other Document'];

        return view('documents.edit', compact('document', 'jenisLayananOptions', 'tipeOrderOptions', 'tipeDokumenOptions'));
    }

    public function update(Request $request, $slug)
    {
        if (!in_array(Auth::user()->role, ['admin', 'inputter'])) {
            abort(403);
        }

        $document = Document::with('documentDetail')->where('slug', $slug)->firstOrFail();

        $request->validate([
            'tipe_dokumen' => 'required|string|max:255',
            'nomor_dokumen' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'nama_pelanggan' => 'nullable|string|max:255',
            'lokasi_kerja' => 'nullable|string|max:255',
            'jenis_layanan' => 'nullable|in:IP Transit,Metro E,CNDC,SL WDM,SCC,Digital',
            'tipe_order' => 'nullable|in:SO,DO,MO,RO',
            'sid' => 'nullable|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            'custom_file_name' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($document->file_path);
            $file = $request->file('file');
            $fileName = $request->filled('custom_file_name')
                ? $request->custom_file_name . '.' . $file->getClientOriginalExtension()
                : $file->getClientOriginalName();

            $document->file_name = $fileName;
            $document->file_path = $file->storeAs('documents', $fileName, 'public');
            $document->file_size = $file->getSize();
            $document->upload_timestamp = now();
        } elseif ($request->filled('custom_file_name')) {
            $oldExtension = pathinfo($document->file_name, PATHINFO_EXTENSION);
            $newFileName = $request->custom_file_name . '.' . $oldExtension;
            $newPath = 'documents/' . $newFileName;

            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->move($document->file_path, $newPath);
            }

            $document->file_name = $newFileName;
            $document->file_path = $newPath;
        }

        $document->save();

        $document->documentDetail->update([
            'tipe_dokumen' => $request->tipe_dokumen,
            'nomor_dokumen' => $request->nomor_dokumen,
            'bak' => $request->has('bak'),
            'ba' => $request->has('ba'),
            'pks' => $request->has('pks'),
            'po' => $request->has('po'),
            'description' => $request->description,
            'nama_pelanggan' => $request->nama_pelanggan,
            'lokasi_kerja' => $request->lokasi_kerja,
            'jenis_layanan' => $request->jenis_layanan,
            'tipe_order' => $request->tipe_order,
            'sid' => $request->sid,
            'tanggal_mulai' => $request->tanggal_mulai,
        ]);

        $subject = $request->nama_pelanggan ?? $request->nomor_dokumen ?? 'document';
        $slug = Str::slug($subject);
        $count = Document::where('slug', 'like', "$slug%")->where('id', '!=', $document->id)->count();
        $document->slug = $count ? "{$slug}-{$count}" : $slug;
        $document->save();

        return redirect()->route('documents.show', $document->slug)->with('success', 'Dokumen berhasil diperbarui!');
    }

    public function destroy($id)
    {
        if (!in_array(Auth::user()->role, ['admin', 'inputter'])) {
            abort(403);
        }

        $document = Document::findOrFail($id);
        Storage::disk('public')->delete($document->file_path);
        $document->documentDetail->delete();
        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil dihapus!');
    }
}
