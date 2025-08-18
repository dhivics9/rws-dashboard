@extends('template.conf')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-2xl font-bold mb-6">Edit Document</h1>

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('documents.update', $document->slug) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
            <!-- Document Type -->
            <div class="mb-5">
                <label for="document_type" class="block text-sm font-medium text-gray-700">Document Type</label>
                <select name="document_type" id="document_type" onchange="showDynamicFields()"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">-- Pilih jenis dokumen --</option>
                    <option value="Berita Acara" {{ old('document_type', $document->documentDetail->document_type) == 'Berita Acara' ? 'selected' : '' }}>Berita Acara</option>
                    <option value="Resignation Letter" {{ old('document_type', $document->documentDetail->document_type) == 'Resignation Letter' ? 'selected' : '' }}>Resignation Letter</option>
                    <option value="Other Document" {{ old('document_type', $document->documentDetail->document_type) == 'Other Document' ? 'selected' : '' }}>Other Document</option>
                </select>
                @error('document_type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                >{{ old('description', $document->documentDetail->description) }}</textarea>
            </div>

            <!-- Dynamic Fields: Berita Acara -->
            <div id="berita-acara-fields" class="hidden space-y-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h3 class="text-lg font-semibold text-blue-800">Detail Berita Acara</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                        <input type="text" name="berita_acara[nama_pelanggan]"
                            value="{{ old('berita_acara.nama_pelanggan', $document->documentDetail->beritaAcara->nama_pelanggan ?? '') }}"
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('berita_acara.nama_pelanggan')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lokasi Kerja</label>
                        <input type="text" name="berita_acara[lokasi_kerja]"
                            value="{{ old('berita_acara.lokasi_kerja', $document->documentDetail->beritaAcara->lokasi_kerja ?? '') }}"
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('berita_acara.lokasi_kerja')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Layanan</label>
                        <input type="text" name="berita_acara[jenis_layanan]"
                            value="{{ old('berita_acara.jenis_layanan', $document->documentDetail->beritaAcara->jenis_layanan ?? '') }}"
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('berita_acara.jenis_layanan')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">MO</label>
                        <input type="text" name="berita_acara[mo]"
                            value="{{ old('berita_acara.mo', $document->documentDetail->beritaAcara->mo ?? '') }}"
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('berita_acara.mo')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">SID</label>
                        <input type="text" name="berita_acara[sid]"
                            value="{{ old('berita_acara.sid', $document->documentDetail->beritaAcara->sid ?? '') }}"
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('berita_acara.sid')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bandwidth Sebelumnya</label>
                        <input type="text" name="berita_acara[bw_prev]"
                            value="{{ old('berita_acara.bw_prev', $document->documentDetail->beritaAcara->bw_prev ?? '') }}"
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('berita_acara.bw_prev')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bandwidth Baru</label>
                        <input type="text" name="berita_acara[bw_new]"
                            value="{{ old('berita_acara.bw_new', $document->documentDetail->beritaAcara->bw_new ?? '') }}"
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('berita_acara.bw_new')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" name="berita_acara[tanggal_mulai]"
                            value="{{ old('berita_acara.tanggal_mulai', $document->documentDetail->beritaAcara->tanggal_mulai ?? '') }}"
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('berita_acara.tanggal_mulai')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Dynamic Fields: Resignation Letter -->
            <div id="resign-letter-fields" class="hidden space-y-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <h3 class="text-lg font-semibold text-yellow-800">Detail Resignation Letter</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee Name</label>
                        <input type="text" name="resign_letter[employee_name]"
                            value="{{ old('resign_letter.employee_name', $document->documentDetail->resignLetter->employee_name ?? '') }}"
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('resign_letter.employee_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employee ID</label>
                        <input type="text" name="resign_letter[employee_id]"
                            value="{{ old('resign_letter.employee_id', $document->documentDetail->resignLetter->employee_id ?? '') }}"
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('resign_letter.employee_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Day of Work</label>
                        <input type="date" name="resign_letter[last_day_of_work]"
                            value="{{ old('resign_letter.last_day_of_work', $document->documentDetail->resignLetter->last_day_of_work ?? '') }}"
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                        @error('resign_letter.last_day_of_work')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reason</label>
                        <textarea name="resign_letter[reason]" rows="3"
                            class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                        >{{ old('resign_letter.reason', $document->documentDetail->resignLetter->reason ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- File Upload -->
            <div class="mb-5">
                <label for="file" class="block text-sm font-medium text-gray-700">Document File (Optional)</label>
                <input type="file" name="file" id="file"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                           file:rounded-md file:border-0 file:text-sm file:font-semibold
                           file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                <p class="text-sm text-gray-500 mt-1">Current: {{ $document->file_name }}</p>
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    Update Document
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function showDynamicFields() {
        const type = document.getElementById('document_type').value;
        const berita = document.getElementById('berita-acara-fields');
        const resign = document.getElementById('resign-letter-fields');

        berita.classList.add('hidden');
        resign.classList.add('hidden');

        if (type === 'Berita Acara') {
            berita.classList.remove('hidden');
        } else if (type === 'Resignation Letter') {
            resign.classList.remove('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        showDynamicFields();
    });
</script>

@endsection
