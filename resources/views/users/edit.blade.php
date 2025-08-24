@extends('template.conf')

@section('title', 'Edit Pengguna')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-dark-blue mb-6">Edit Pengguna</h1>

    <div class="bg-white p-6 rounded shadow">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-dark-blue focus:border-dark-blue" required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-dark-blue focus:border-dark-blue" required>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password (Opsional)</label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-dark-blue focus:border-dark-blue" placeholder="Kosongkan jika tidak ingin mengubah">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-dark-blue focus:border-dark-blue">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" id="role" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-dark-blue focus:border-dark-blue" required>
                        <option value="">Pilih Role</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="inputter" {{ $user->role === 'inputter' ? 'selected' : '' }}>Inputter</option>
                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('users.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 mr-2">Batal</a>
                <button type="submit" class="bg-dark-blue text-white px-4 py-2 rounded hover:bg-gray-700">Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection
