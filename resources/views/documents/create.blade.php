<!-- resources/views/documents/create.blade.php -->
@extends('template.conf')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-2xl font-bold mb-6">Submit New Document</h1>

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow">
            <!-- Nama Dokumen (File Name) -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Nama Dokumen (Nama File)</label>
                <input type="text" name="custom_file_name"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm"
                       placeholder="Masukkan nama dokumen tanpa ekstensi"
                       value="{{ old('custom_file_name') }}">
                <p class="text-sm text-gray-500 mt-1">Nama file akan menjadi: <span id="file_name_preview"></span></p>
            </div>

            <!-- Tipe Dokumen -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Tipe Dokumen</label>
                <select name="tipe_dokumen" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    <option value="">-- Pilih tipe dokumen --</option>
                    <option value="Berita Acara" {{ old('tipe_dokumen') == 'Berita Acara' ? 'selected' : '' }}>Berita Acara</option>
                    <option value="BAK" {{ old('tipe_dokumen') == 'BAK' ? 'selected' : '' }}>BAK</option>
                    <option value="BA" {{ old('tipe_dokumen') == 'BA' ? 'selected' : '' }}>BA</option>
                    <option value="PKS" {{ old('tipe_dokumen') == 'PKS' ? 'selected' : '' }}>PKS</option>
                    <option value="PO" {{ old('tipe_dokumen') == 'PO' ? 'selected' : '' }}>PO</option>
                    <option value="Other Document" {{ old('tipe_dokumen') == 'Other Document' ? 'selected' : '' }}>Other Document</option>
                </select>
                @error('tipe_dokumen')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nomor Dokumen -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Nomor Dokumen</label>
                <input type="text" name="nomor_dokumen" value="{{ old('nomor_dokumen') }}"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm"
                       placeholder="Contoh: 001/BAK/2025">
                @error('nomor_dokumen')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Pelanggan -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                @error('nama_pelanggan')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lokasi Kerja -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Lokasi Kerja</label>
                <input type="text" name="lokasi_kerja" value="{{ old('lokasi_kerja') }}"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                @error('lokasi_kerja')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jenis Layanan (Dropdown, opsional) -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Jenis Layanan</label>
                <select name="jenis_layanan" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    <option value="">Tidak dipilih</option>
                    <option value="IP Transit" {{ old('jenis_layanan') == 'IP Transit' ? 'selected' : '' }}>IP Transit</option>
                    <option value="Metro E" {{ old('jenis_layanan') == 'Metro E' ? 'selected' : '' }}>Metro E</option>
                    <option value="CNDC" {{ old('jenis_layanan') == 'CNDC' ? 'selected' : '' }}>CNDC</option>
                    <option value="SL WDM" {{ old('jenis_layanan') == 'SL WDM' ? 'selected' : '' }}>SL WDM</option>
                    <option value="SCC" {{ old('jenis_layanan') == 'SCC' ? 'selected' : '' }}>SCC</option>
                    <option value="Digital" {{ old('jenis_layanan') == 'Digital' ? 'selected' : '' }}>Digital</option>
                </select>
                @error('jenis_layanan')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipe Order (Dropdown) -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Tipe Order</label>
                <select name="tipe_order" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                    <option value="">Tidak dipilih</option>
                    <option value="SO" {{ old('tipe_order') == 'SO' ? 'selected' : '' }}>SO</option>
                    <option value="DO" {{ old('tipe_order') == 'DO' ? 'selected' : '' }}>DO</option>
                    <option value="MO" {{ old('tipe_order') == 'MO' ? 'selected' : '' }}>MO</option>
                    <option value="RO" {{ old('tipe_order') == 'RO' ? 'selected' : '' }}>RO</option>
                </select>
                @error('tipe_order')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- SID -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">SID</label>
                <input type="text" name="sid" value="{{ old('sid') }}"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                @error('sid')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Mulai -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">
                @error('tanggal_mulai')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">File Dokumen</label>
                <input type="file" name="file" required
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0 file:text-sm file:font-semibold
                              file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                @error('file')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button type="submit"
                        class="w-full py-2 px-4 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                    Submit Document
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
        let fileName = 'Tidak ada file dipilih';

        if (fileInput.files.length > 0) {
            const original = fileInput.files[0].name;
            const ext = original.split('.').pop();
            fileName = customName ? `${customName}.${ext}` : original;
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
