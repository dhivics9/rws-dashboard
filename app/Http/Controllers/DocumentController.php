<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentDetail;
use App\Models\DetailsBeritaAcara;
use App\Models\DetailsResignLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        // Hanya user yang terautentikasi yang bisa melihat daftar dokumen
        if (!Auth::check()) {
            return redirect('/login');
        }

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
        // Hanya admin dan inputter yang bisa membuat dokumen
        if (!in_array(Auth::user()->role, ['admin', 'inputter'])) {
            abort(403, 'Unauthorized access');
        }

        return view('documents.create');
    }

    public function store(Request $request)
    {
        // Hanya admin dan inputter yang bisa menyimpan dokumen
        if (!in_array(Auth::user()->role, ['admin', 'inputter'])) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'document_type' => 'required|in:Berita Acara,Resignation Letter,Other Document',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
            'custom_file_name' => 'nullable|string|max:255', // Validasi untuk custom name
        ]);

        $file = $request->file('file');

        // Gunakan custom name jika ada, jika tidak gunakan nama asli file
        $fileName = $request->filled('custom_file_name')
            ? $request->custom_file_name . '.' . $file->getClientOriginalExtension()
            : $file->getClientOriginalName();

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
            $request->validate([
                'berita_acara.nama_pelanggan' => 'required|string|max:255',
                'berita_acara.lokasi_kerja' => 'required|string|max:255',
                'berita_acara.jenis_layanan' => 'required|string|max:255',
                'berita_acara.mo' => 'required|string|max:255',
                'berita_acara.sid' => 'required|string|max:255',
                'berita_acara.bw_prev' => 'required|string|max:255',
                'berita_acara.bw_new' => 'required|string|max:255',
                'berita_acara.tanggal_mulai' => 'required|date',
            ]);

            $berita = $request->input('berita_acara');

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
            $request->validate([
                'resign_letter.employee_name' => 'required|string|max:255',
                'resign_letter.employee_id' => 'required|string|max:255',
                'resign_letter.last_day_of_work' => 'required|date',
                'resign_letter.reason' => 'nullable|string',
            ]);

            $resign = $request->input('resign_letter');

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
        // Semua user yang terautentikasi bisa melihat detail dokumen
        if (!Auth::check()) {
            return redirect('/login');
        }

        $document = Document::where('slug', $slug)->firstOrFail();

        return view('documents.show', compact('document'));
    }

    public function edit($slug)
    {
        // Hanya admin dan inputter yang bisa mengedit dokumen
        if (!in_array(Auth::user()->role, ['admin', 'inputter'])) {
            abort(403, 'Unauthorized access');
        }

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
        // Hanya admin dan inputter yang bisa mengupdate dokumen
        if (!in_array(Auth::user()->role, ['admin', 'inputter'])) {
            abort(403, 'Unauthorized access');
        }

        $document = Document::where('slug', $slug)
            ->with(['documentDetail', 'documentDetail.beritaAcara', 'documentDetail.resignLetter'])
            ->firstOrFail();

        $request->validate([
            'document_type' => 'required|in:Berita Acara,Resignation Letter,Other Document',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
            'custom_file_name' => 'nullable|string|max:255', // Validasi untuk custom name
        ]);

        // Update file jika ada
        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($document->file_path);
            $file = $request->file('file');

            // Gunakan custom name jika ada, jika tidak gunakan nama asli file
            $fileName = $request->filled('custom_file_name')
                ? $request->custom_file_name . '.' . $file->getClientOriginalExtension()
                : $file->getClientOriginalName();

            $document->file_name = $fileName;
            $document->file_path = $file->storeAs('documents', $fileName, 'public');
            $document->file_size = $file->getSize();
            $document->upload_timestamp = now();
        } else if ($request->filled('custom_file_name')) {
            // Jika tidak upload file baru tapi ingin ganti nama file
            $oldExtension = pathinfo($document->file_name, PATHINFO_EXTENSION);
            $newFileName = $request->custom_file_name . '.' . $oldExtension;

            // Rename file di storage
            $newPath = 'documents/' . $newFileName;
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->move($document->file_path, $newPath);
            }

            $document->file_name = $newFileName;
            $document->file_path = $newPath;
        }

        $documentDetail = $document->documentDetail;
        $documentDetail->update([
            'document_type' => $request->document_type,
            'description' => $request->description,
        ]);

        $subject = '';

        if ($request->document_type === 'Berita Acara') {
            $request->validate([
                'berita_acara.nama_pelanggan' => 'required|string|max:255',
                'berita_acara.lokasi_kerja' => 'required|string|max:255',
                'berita_acara.jenis_layanan' => 'required|string|max:255',
                'berita_acara.mo' => 'required|string|max:255',
                'berita_acara.sid' => 'required|string|max:255',
                'berita_acara.bw_prev' => 'required|string|max:255',
                'berita_acara.bw_new' => 'required|string|max:255',
                'berita_acara.tanggal_mulai' => 'required|date',
            ]);

            $berita = $request->input('berita_acara');

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
            $request->validate([
                'resign_letter.employee_name' => 'required|string|max:255',
                'resign_letter.employee_id' => 'required|string|max:255',
                'resign_letter.last_day_of_work' => 'required|date',
                'resign_letter.reason' => 'nullable|string',
            ]);

            $resign = $request->input('resign_letter');

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
        // Hanya admin dan inputter yang bisa menghapus dokumen
        if (!in_array(Auth::user()->role, ['admin', 'inputter'])) {
            abort(403, 'Unauthorized access');
        }

        $document = Document::findOrFail($id);
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->back()->with('success', 'Document deleted!');
    }
}
