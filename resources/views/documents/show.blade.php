<!-- resources/views/documents/show.blade.php -->
@extends('template.conf')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <h1 class="text-2xl font-bold">{{ $document->file_name }}</h1>
            <a href="{{ route('documents.index') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 transition">← Back</a>
        </div>

        <!-- Meta Info -->
        <div class="bg-white p-5 rounded-lg shadow mb-6">
            <p><strong>Tipe Dokumen:</strong> {{ $document->documentDetail->tipe_dokumen }}</p>
            <p><strong>Nomor Dokumen:</strong> {{ $document->documentDetail->nomor_dokumen ?? '-' }}</p>
            <p><strong>BAK:</strong> {{ $document->documentDetail->bak ? 'Ya' : 'Tidak' }}</p>
            <p><strong>BA:</strong> {{ $document->documentDetail->ba ? 'Ya' : 'Tidak' }}</p>
            <p><strong>PKS:</strong> {{ $document->documentDetail->pks ? 'Ya' : 'Tidak' }}</p>
            <p><strong>PO:</strong> {{ $document->documentDetail->po ? 'Ya' : 'Tidak' }}</p>
            <p><strong>Deskripsi:</strong> {{ $document->documentDetail->description ?? '-' }}</p>
            <p><strong>Uploaded By:</strong> {{ $document->user->name ?? 'Unknown' }}</p>
            <p><strong>Uploaded On:</strong> {{ $document->upload_timestamp->format('d/m/Y H:i') }}</p>
        </div>

        <!-- Detail Informasi -->
        <div class="bg-white p-5 rounded-lg shadow mb-6">
            <h3 class="text-lg font-semibold mb-4">Detail Informasi</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-y-3">
                <div><strong>Nama Pelanggan:</strong> {{ $document->documentDetail->nama_pelanggan ?? '-' }}</div>
                <div><strong>Lokasi Kerja:</strong> {{ $document->documentDetail->lokasi_kerja ?? '-' }}</div>
                <div><strong>Jenis Layanan:</strong> {{ $document->documentDetail->jenis_layanan ?? '-' }}</div>
                <div><strong>Tipe Order:</strong> {{ $document->documentDetail->tipe_order ?? '-' }}</div>
                <div><strong>SID:</strong> {{ $document->documentDetail->sid ?? '-' }}</div>
                {{-- @dd( $document->documentDetail->tanggal_mulai) --}}
                <div><strong>Tanggal Mulai:</strong> {{ $document->documentDetail->tanggal_mulai ?? '-' }}</div>
            </dl>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 mb-6">
            <a href="{{ asset('storage/' . $document->file_path) }}" download
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Download File
            </a>
            <a href="{{ route('documents.edit', $document->slug) }}"
               class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">Edit</a>
        </div>

        <!-- File Preview -->
        <div class="bg-white p-5 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">File Preview</h3>
            @if (strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION)) === 'pdf')
                <iframe src="{{ asset('storage/' . $document->file_path) }}" width="100%" height="600" style="border: none; border-radius: 8px;"></iframe>
            @else
                <pre class="bg-gray-900 text-green-400 p-4 rounded-md overflow-auto max-h-96">{{ file_exists(storage_path('app/public/' . $document->file_path)) ? file_get_contents(storage_path('app/public/' . $document->file_path)) : 'File tidak tersedia' }}</pre>
            @endif
        </div>
    </div>
</div>
@endsection
