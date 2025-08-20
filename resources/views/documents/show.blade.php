@extends('template.conf')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-start mb-6">
                <h1 class="text-2xl font-bold">{{ $document->file_name }}</h1>
                <a href="{{ route('documents.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 transition">← Back to List</a>
            </div>

            <!-- Meta Info -->
            <div class="bg-white p-5 rounded-lg shadow mb-6">
                <p><strong>Document Type:</strong> {{ $document->documentDetail->document_type }}</p>
                <p><strong>Description:</strong> {{ $document->documentDetail->description ?? '-' }}</p>
                <p><strong>Uploaded On:</strong> {{ $document->upload_timestamp->format('m/d/Y, H:i A') }}</p>
            </div>

            <!-- Detail Spesifik -->
            @if ($document->documentDetail->document_type === 'Berita Acara')
                <div class="bg-blue-50 p-5 rounded-lg border border-blue-200 mb-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4">Detail Berita Acara</h3>
                    <dl class="grid grid-cols-1 gap-y-3">
                        <div><strong>Nama Pelanggan:</strong> {{ $document->documentDetail->beritaAcara->nama_pelanggan }}
                        </div>
                        <div><strong>Lokasi Kerja:</strong> {{ $document->documentDetail->beritaAcara->lokasi_kerja }}</div>
                        <div><strong>Jenis Layanan:</strong> {{ $document->documentDetail->beritaAcara->jenis_layanan }}
                        </div>
                        <div><strong>MO:</strong> {{ $document->documentDetail->beritaAcara->mo }}</div>
                        <div><strong>SID:</strong> {{ $document->documentDetail->beritaAcara->sid }}</div>
                        <div><strong>Bandwidth Sebelumnya:</strong> {{ $document->documentDetail->beritaAcara->bw_prev }}
                        </div>
                        <div><strong>Bandwidth Baru:</strong> {{ $document->documentDetail->beritaAcara->bw_new }}</div>
                        <div><strong>Tanggal Mulai:</strong> {{ $document->documentDetail->beritaAcara->tanggal_mulai }}
                        </div>
                    </dl>
                </div>
            @elseif ($document->documentDetail->document_type === 'Resignation Letter')
                <div class="bg-yellow-50 p-5 rounded-lg border border-yellow-200 mb-6">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-4">Detail Resignation Letter</h3>
                    <dl class="grid grid-cols-1 gap-y-3">
                        <div><strong>Employee Name:</strong> {{ $document->documentDetail->resignLetter->employee_name }}
                        </div>
                        <div><strong>Employee ID:</strong> {{ $document->documentDetail->resignLetter->employee_id }}</div>
                        <div><strong>Last Day of Work:</strong>
                            {{ $document->documentDetail->resignLetter->last_day_of_work }}</div>
                        <div><strong>Reason:</strong> {{ $document->documentDetail->resignLetter->reason ?? '-' }}</div>
                    </dl>
                </div>
            @else
                <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">General Document</h3>
                    <p>This is a general document with no specific details. Please refer to the description and the file
                        preview.</p>
                </div>
            @endif

            <!-- Download Button -->
            <div class="mb-6">
                <a href="{{ asset('storage/' . $document->file_path) }}"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                    download>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Download File
                </a>
            </div>

            <!-- File Preview -->
            <div class="bg-white p-5 rounded-lg shadow">
                <h3 class="text-lg font-medium mb-4">File Preview</h3>
                @if (pathinfo($document->file_name, PATHINFO_EXTENSION) === 'pdf')
                    <iframe src="{{ Storage::url($document->file_path) }}" width="100%" height="600"
                        style="border: none; border-radius: 8px;" title="PDF Preview"></iframe>
                @else
                    <pre class="bg-gray-900 text-green-400 p-4 rounded-md overflow-auto max-h-96">
{{ file_get_contents(storage_path('app/public/' . $document->file_path)) }}
                </pre>
                @endif
            </div>
        </div>
    </div>
@endsection
