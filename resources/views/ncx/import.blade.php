@extends('template.conf')

@section('content')
    <div class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full mx-4 border border-gray-200">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Import Target OGD</h2>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <p class="text-gray-600 text-center mb-6">
                Unggah file Excel atau CSV untuk mengimpor data ke sistem.
            </p>

            <!-- Form Upload -->
            <form id="import-form" action="{{ route('ncx-status.import') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4">
                @csrf
                <label for="file-upload" class="block cursor-pointer">
                    <div
                        class="border-2 border-dashed border-gray-400 rounded-lg p-6 text-center hover:border-blue-500 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span class="mt-2 block text-sm font-medium text-gray-600">
                            Pilih file atau seret ke sini
                        </span>
                        <span class="text-xs text-gray-500">CSV, XLSX </span>
                    </div>
                    <input id="file-upload" name="file" type="file" class="sr-only" accept=".csv,.xlsx,.xls" required
                        onchange="document.getElementById('file-name').textContent = this.files[0]?.name || ''" />
                </label>

                <!-- Tampilkan nama file -->
                <div id="file-name-container" class="text-center mt-2">
                    <span id="file-name" class="text-sm text-gray-500 italic">Belum ada file dipilih</span>
                </div>

                <!-- Tombol Import dengan Loading State -->
                <button id="import-button" type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Import Data
                </button>
            </form>

        </div>
    </div>

    <!-- Script: Loading State -->
    <script>
        document.getElementById('import-form').addEventListener('submit', function(e) {
            const button = document.getElementById('import-button');
            const originalText = button.textContent;

            // Cegah submit jika tidak ada file
            const fileInput = document.getElementById('file-upload');
            if (!fileInput.files.length) {
                return; // Biarkan validasi HTML berjalan
            }

            // Nonaktifkan tombol & tampilkan loading
            button.disabled = true;
            button.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Memproses...
      `;
        });

        // Reset nama file saat klik ulang
        document.getElementById('file-upload').addEventListener('click', function() {
            document.getElementById('file-name').textContent = 'Belum ada file dipilih';
        });
    </script>
@endsection
