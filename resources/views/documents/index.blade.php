@extends('template.conf')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <h1 class="text-2xl font-bold mb-6">Document Repository</h1>

        <a href="{{ route('documents.create') }}"
            class="mb-6 inline-block px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">+ New
            Document</a>

        <form method="GET" action="{{ route('documents.index') }}" class="flex flex-col md:flex-row gap-4 mb-6">
            <div class="flex-grow relative">
                <input type="text" name="search" placeholder="Cari berdasarkan nama file, tipe dokumen, subjek, atau uploader"
                    value="{{ request('search') }}"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-2.5 text-gray-400" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <select name="type"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Semua Tipe Dokumen</option>
                <option value="Berita Acara" {{ request('type') == 'Berita Acara' ? 'selected' : '' }}>Berita Acara</option>
                <option value="Resignation Letter" {{ request('type') == 'Resignation Letter' ? 'selected' : '' }}>
                    Resignation Letter</option>
                <option value="Other Document" {{ request('type') == 'Other Document' ? 'selected' : '' }}>Other Document
                </option>
            </select>
            <button type="submit"
                class="px-4 py-2 bg-green-600 text-white hover:bg-green-700 rounded-lg transition">Cari</button>
            @if (request()->has('search') || (request()->has('type') && request('type') != 'all'))
                <a href="{{ route('documents.index') }}"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg self-center">Reset</a>
            @endif
        </form>

        @if (request()->has('search') && $documents->isEmpty())
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <p class="text-gray-600">Tidak ada dokumen yang ditemukan</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe
                                Dokumen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subjek
                                Utama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                                File
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Uploaded By
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                                Upload</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($documents as $doc)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $doc->documentDetail->document_type }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if ($doc->documentDetail->beritaAcara)
                                        {{ $doc->documentDetail->beritaAcara->nama_pelanggan }}
                                    @elseif($doc->documentDetail->resignLetter)
                                        {{ $doc->documentDetail->resignLetter->employee_name }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $doc->file_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $doc->user->name ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $doc->upload_timestamp->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('documents.show', $doc->slug) }}"
                                        class="text-blue-600 hover:text-blue-800 mr-2">View</a> <a
                                        href="{{ route('documents.edit', $doc->slug) }}"
                                        class="text-yellow-600 hover:text-yellow-800 mr-2">Edit</a>
                                    <form action="{{ route('documents.destroy', $doc) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $documents->appends(request()->query())->links() }}
        @endif
    </div>
@endsection
