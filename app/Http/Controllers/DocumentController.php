<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentDetail;
use App\Models\DetailsBeritaAcara;
use App\Models\DetailsResignLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with(['documentDetail' => function ($q) {
            $q->with(['beritaAcara', 'resignLetter']);
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('documentDetail', function ($q) use ($search) {
                $q->where('document_type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->whereHas('documentDetail', function ($q) use ($request) {
                $q->where('document_type', $request->type);
            });
        }

        $documents = $query->paginate(10);

        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:Berita Acara,Resignation Letter,Other Document',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        $document = Document::create([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'upload_timestamp' => now(),
            'slug' => null, // akan diisi nanti
        ]);

        $documentDetail = DocumentDetail::create([
            'document_type' => $request->document_type,
            'description' => $request->description,
            'document_id' => $document->id,
        ]);

        $subject = '';

        if ($request->document_type === 'Berita Acara') {
            $berita = $request->input('berita_acara');
            // ... validasi ...

            $beritaAcara = DetailsBeritaAcara::create([
                'document_detail_id' => $documentDetail->id,
                'nama_pelanggan' => $berita['nama_pelanggan'],
                'lokasi_kerja' => $berita['lokasi_kerja'],
                'jenis_layanan' => $berita['jenis_layanan'],
                'mo' => $berita['mo'],
                'sid' => $berita['sid'],
                'bw_prev' => $berita['bw_prev'],
                'bw_new' => $berita['bw_new'],
                'tanggal_mulai' => $berita['tanggal_mulai'],
            ]);

            $subject = $berita['nama_pelanggan'];
        }

        if ($request->document_type === 'Resignation Letter') {
            $resign = $request->input('resign_letter');
            // ... validasi ...

            $resignLetter = DetailsResignLetter::create([
                'document_detail_id' => $documentDetail->id,
                'employee_name' => $resign['employee_name'],
                'employee_id' => $resign['employee_id'],
                'last_day_of_work' => $resign['last_day_of_work'],
                'reason' => $resign['reason'] ?? null,
            ]);

            $subject = $resign['employee_name'];
        }

        // Sekarang set slug dari subject
        $slug = Str::slug($subject);

        // Cek duplikat
        $count = Document::where('slug', 'like', "{$slug}%")->count();
        $document->slug = $count ? "{$slug}-{$count}" : $slug;
        $document->save();

        return redirect()->route('documents.show', $document->slug)->with('success', 'Document uploaded successfully!');
    }
    public function show($slug)
    {
        $document = Document::where('slug', $slug)->firstOrFail();
        $document->load(['documentDetail', 'documentDetail.beritaAcara', 'documentDetail.resignLetter']);

        return view('documents.show', compact('document'));
    }

    public function edit($slug)
    {
        $document = Document::where('slug', $slug)
            ->with([
                'documentDetail',
                'documentDetail.beritaAcara',
                'documentDetail.resignLetter'
            ])->firstOrFail();

        return view('documents.edit', compact('document'));
    }

    public function update(Request $request, $slug)
    {
        $document = Document::where('slug', $slug)
            ->with(['documentDetail', 'documentDetail.beritaAcara', 'documentDetail.resignLetter'])
            ->firstOrFail();

        $request->validate([
            'document_type' => 'required|in:Berita Acara,Resignation Letter,Other Document',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        // Update file jika ada
        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($document->file_path);
            $file = $request->file('file');
            $document->file_name = $file->getClientOriginalName();
            $document->file_path = $file->storeAs('documents', $document->file_name, 'public');
            $document->file_size = $file->getSize();
            $document->upload_timestamp = now();
        }

        $documentDetail = $document->documentDetail;
        $documentDetail->update([
            'document_type' => $request->document_type,
            'description' => $request->description,
        ]);

        $subject = '';

        if ($request->document_type === 'Berita Acara') {
            $berita = $request->input('berita_acara');
            // ... validasi ...

            if ($documentDetail->beritaAcara) {
                $documentDetail->beritaAcara->update($berita);
            } else {
                DetailsBeritaAcara::create(['document_detail_id' => $documentDetail->id, ...$berita]);
            }

            $subject = $berita['nama_pelanggan'];
        } else {
            $documentDetail->beritaAcara?->delete();
        }

        if ($request->document_type === 'Resignation Letter') {
            $resign = $request->input('resign_letter');
            // ... validasi ...

            if ($documentDetail->resignLetter) {
                $documentDetail->resignLetter->update($resign);
            } else {
                DetailsResignLetter::create(['document_detail_id' => $documentDetail->id, ...$resign]);
            }

            $subject = $resign['employee_name'];
        } else {
            $documentDetail->resignLetter?->delete();
        }

        // Update slug dari subject utama
        $slug = Str::slug($subject);
        $count = Document::where('slug', 'like', "{$slug}%")->where('id', '!=', $document->id)->count();
        $document->slug = $count ? "{$slug}-{$count}" : $slug;

        $document->save();

        return redirect()->route('documents.show', $document->slug)->with('success', 'Document updated successfully!');
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->back()->with('success', 'Document deleted!');
    }
}
