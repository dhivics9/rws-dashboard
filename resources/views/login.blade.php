@extends('template.conf')

@section('content')
    <div class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8 border border-gray-200">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Masuk ke Akun Anda</h2>
                <p class="text-gray-500 mt-2">Silakan masukkan kredensial untuk melanjutkan.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                        placeholder="contoh@perusahaan.com" />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                        placeholder="••••••••" />
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only" />
                        <div class="relative w-11 h-6 bg-gray-200 rounded-full peer">
                            <div
                                class="absolute left-0 top-0 w-6 h-6 bg-white rounded-full peer-checked:translate-x-6 transition-transform duration-300">
                            </div>
                        </div>
                        <span class="ml-2 text-sm text-gray-600">Ingat Saya</span>
                    </label>
                    <a href="#" class="text-sm text-emerald-600 hover:underline">Lupa Kata Sandi?</a>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg transition transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                    Masuk
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun?
                    <a href="#" class="text-emerald-600 hover:underline font-medium">Daftar Sekarang</a>
                </p>
            </div>
        </div>
    </div>
@endsection
