<!-- resources/views/documents/edit.blade.php -->
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
        <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow">
            <!-- Nama Dokumen -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Nama Dokumen (Nama File)</label>
                <input type="text" name="custom_file_name" value="{{ old('custom_file_name', pathinfo($document->file_name, PATHINFO_FILENAME)) }}"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm"
                       placeholder="Masukkan nama dokumen tanpa ekstensi">
                <p class="text-sm text-gray-500 mt-1">
                    Current: {{ $document->file_name }}<br>
                    New name: <span id="file_name_preview"></span>
                </p>
            </div>

            <!-- Tipe Dokumen -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Tipe Dokumen</label>
                <select name="tipe_dokumen" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    <option value="">-- Pilih tipe dokumen --</option>
                    <option value="BAK" {{ old('tipe_dokumen', $document->documentDetail->tipe_dokumen) == 'BAK' ? 'selected' : '' }}>BAK</option>
                    <option value="BA" {{ old('tipe_dokumen', $document->documentDetail->tipe_dokumen) == 'BA' ? 'selected' : '' }}>BA</option>
                    <option value="PKS" {{ old('tipe_dokumen', $document->documentDetail->tipe_dokumen) == 'PKS' ? 'selected' : '' }}>PKS</option>
                    <option value="PO" {{ old('tipe_dokumen', $document->documentDetail->tipe_dokumen) == 'PO' ? 'selected' : '' }}>PO</option>
                    <option value="Other Document" {{ old('tipe_dokumen', $document->documentDetail->tipe_dokumen) == 'Other Document' ? 'selected' : '' }}>Other Document</option>
                </select>
            </div>

            <!-- Nomor Dokumen -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Nomor Dokumen</label>
                <input type="text" name="nomor_dokumen" value="{{ old('nomor_dokumen', $document->documentDetail->nomor_dokumen) }}"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
            </div>

            <!-- Nama Pelanggan -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan', $document->documentDetail->nama_pelanggan) }}"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
            </div>

            <!-- Lokasi Kerja -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Lokasi Kerja</label>
                <input type="text" name="lokasi_kerja" value="{{ old('lokasi_kerja', $document->documentDetail->lokasi_kerja) }}"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
            </div>

            <!-- Jenis Layanan -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Jenis Layanan</label>
                <select name="jenis_layanan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    <option value="">Tidak dipilih</option>
                    <option value="IP Transit" {{ old('jenis_layanan', $document->documentDetail->jenis_layanan) == 'IP Transit' ? 'selected' : '' }}>IP Transit</option>
                    <option value="Metro E" {{ old('jenis_layanan', $document->documentDetail->jenis_layanan) == 'Metro E' ? 'selected' : '' }}>Metro E</option>
                    <option value="CNDC" {{ old('jenis_layanan', $document->documentDetail->jenis_layanan) == 'CNDC' ? 'selected' : '' }}>CNDC</option>
                    <option value="SL WDM" {{ old('jenis_layanan', $document->documentDetail->jenis_layanan) == 'SL WDM' ? 'selected' : '' }}>SL WDM</option>
                    <option value="SCC" {{ old('jenis_layanan', $document->documentDetail->jenis_layanan) == 'SCC' ? 'selected' : '' }}>SCC</option>
                    <option value="Digital" {{ old('jenis_layanan', $document->documentDetail->jenis_layanan) == 'Digital' ? 'selected' : '' }}>Digital</option>
                </select>
            </div>

            <!-- Tipe Order (Dropdown) -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Tipe Order</label>
                <select name="tipe_order" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    <option value="">Tidak dipilih</option>
                    <option value="SO" {{ old('tipe_order', $document->documentDetail->tipe_order) == 'SO' ? 'selected' : '' }}>SO</option>
                    <option value="DO" {{ old('tipe_order', $document->documentDetail->tipe_order) == 'DO' ? 'selected' : '' }}>DO</option>
                    <option value="MO" {{ old('tipe_order', $document->documentDetail->tipe_order) == 'MO' ? 'selected' : '' }}>MO</option>
                    <option value="RO" {{ old('tipe_order', $document->documentDetail->tipe_order) == 'RO' ? 'selected' : '' }}>RO</option>
                </select>
            </div>

            <!-- SID -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">SID</label>
                <input type="text" name="sid" value="{{ old('sid', $document->documentDetail->sid) }}"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
            </div>

            <!-- Tanggal -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $document->documentDetail->tanggal_mulai) }}"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
            </div>

            <!-- Deskripsi -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">{{ old('description', $document->documentDetail->description) }}</textarea>
            </div>

            <!-- File Upload -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">File Dokumen (Optional)</label>
                <input type="file" name="file"
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0 file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
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
    function updateFileNamePreview() {
        const customName = document.querySelector('input[name="custom_file_name"]').value;
        const fileInput = document.querySelector('input[name="file"]');
        const preview = document.getElementById('file_name_preview');
        const currentExtension = '{{ pathinfo($document->file_name, PATHINFO_EXTENSION) }}';
        let fileName = '{{ $document->file_name }}';

        if (fileInput.files.length > 0) {
            const ext = fileInput.files[0].name.split('.').pop();
            fileName = customName ? `${customName}.${ext}` : fileInput.files[0].name;
        } else if (customName) {
            fileName = `${customName}.${currentExtension}`;
        }
        preview.textContent = fileName;
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateFileNamePreview();
        document.querySelector('input[name="custom_file_name"]').addEventListener('input', updateFileNamePreview);
        document.querySelector('input[name="file"]').addEventListener('change', updateFileNamePreview);
    });
</script>
@endsection
