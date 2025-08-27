@extends('template.conf')

@section('title', 'Sinkronisasi Data')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-slate-800 flex items-center justify-center gap-3">
                <span>🔄</span> Sinkronisasi Data dari API Pusat
            </h1>
            <p class="mt-3 text-lg text-slate-600">
                Sinkronkan data terbaru dari sistem pusat ke database lokal.
            </p>
            <p class="mt-2 text-sm text-slate-500 italic">
                <strong class="text-red-600">Catatan:</strong> Proses ini akan menghapus data lama dan menggantinya dengan data baru.
            </p>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div id="alert-success" class="mb-6 p-4 rounded-lg bg-green-100 border border-green-200 text-green-800 text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div id="alert-error" class="mb-6 p-4 rounded-lg bg-red-100 border border-red-200 text-red-800 text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Sync Button - DITENGAH -->
        <div class="flex justify-center mb-10">
            <form id="sync-form" action="{{ route('sync.run') }}" method="POST" class="inline">
                @csrf
                <button id="sync-button" type="submit"
                    class="group flex items-center gap-3 px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300"
                    x-data="{ loading: false }"
                    x-bind:disabled="loading"
                    x-on:click="loading = true; $el.disabled = true; document.getElementById('loading-overlay').classList.remove('hidden');">
                    <span>🔄</span>
                    <span id="button-text">Mulai Sinkronisasi</span>
                </button>
            </form>
        </div>

        <!-- Loading Overlay -->
        <div id="loading-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-xl shadow-2xl text-center max-w-xs">
                <svg class="animate-spin h-10 w-10 text-blue-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-slate-800">Sedang Menyinkronkan...</h3>
                <p class="text-sm text-slate-500 mt-1">Harap tunggu, jangan tutup halaman.</p>
            </div>
        </div>

        <!-- API Endpoints -->
        <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden max-w-3xl mx-auto">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <h2 class="text-xl font-semibold text-slate-700">Endpoint API</h2>
            </div>
            <div class="p-6">
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="text-green-500 mt-1">🟢</span>
                        <div>
                            <strong class="text-slate-800">Revenue:</strong>
                            <code class="ml-2 px-2 py-1 bg-slate-100 text-slate-800 text-sm rounded break-all">{{ env('CENTRAL_API_REVENUE_URL') }}</code>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-500 mt-1">🔵</span>
                        <div>
                            <strong class="text-slate-800">NCX:</strong>
                            <code class="ml-2 px-2 py-1 bg-slate-100 text-slate-800 text-sm rounded break-all">{{ env('CENTRAL_API_NCX_URL') }}</code>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Info Footer -->
        <div class="mt-8 text-center text-sm text-slate-500">
            <p>🔧 Pastikan Anda terhubung ke <strong>VPN GlobalProtect Telkom</strong> untuk mengakses API.</p>
            <p class="mt-1">🔐 Kredensial API diambil dari file <code class="bg-slate-200 px-1 rounded">.env</code>.</p>
        </div>
    </div>

    <!-- Script: Disable form setelah submit -->
    <script>
        document.getElementById('sync-form').addEventListener('submit', function () {
            const button = document.getElementById('sync-button');
            const text = document.getElementById('button-text');

            // Ubah tampilan tombol
            button.disabled = true;
            button.classList.remove('hover:scale-105', 'bg-blue-600', 'hover:bg-blue-700');
            button.classList.add('bg-blue-400', 'cursor-not-allowed');
            text.textContent = 'Sedang Proses...';

            // Tampilkan overlay loading
            document.getElementById('loading-overlay').classList.remove('hidden');
        });

        // Optional: Hilangkan alert setelah 5 detik
        setTimeout(() => {
            document.getElementById('alert-success')?.remove();
            document.getElementById('alert-error')?.remove();
        }, 5000);
    </script>
</div>
@endsection
