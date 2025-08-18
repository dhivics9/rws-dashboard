@extends('template.conf')

@section('content')

    <h1 class="text-2xl font-bold text-gray-900 mb-8">Status NCX</h1>
    @include('atoms.filter_ncx')
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-gray-800">Status NCX - {{ str_replace('-', '/', $selectedPeriod) }}</h1>
        <div class="flex space-x-2">
            <button onclick="exportToExcel()"
                class="px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition duration-150">Excel</button>
            <button onclick="exportToJPEG()"
                class="px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition duration-150">JPEG</button>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse border border-gray-300 bg-white shadow-sm">
            <thead>
                <tr class="bg-orange-500 text-white">
                    <th rowspan="2" class="border border-orange-600 p-3 text-left font-semibold">Layanan</th>
                    <th colspan="{{ count($statuses) }}" class="border border-orange-600 p-3 text-center font-semibold">
                        Status NCX</th>
                    <th rowspan="2" class="border border-orange-600 p-3 text-center font-semibold">TOTAL</th>
                </tr>
                <tr class="bg-orange-500 text-white">
                    @foreach ($statuses as $status)
                        <th class="border border-orange-600 p-2 text-center text-xs">{{ $status }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @forelse ($data as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 p-3 font-medium">{{ $row['layanan'] }}</td>
                        @foreach ($statuses as $status)
                            <td class="border border-gray-300 p-3 text-center">
                                {{ $row['counts'][$status] > 0 ? $row['counts'][$status] : '–' }}
                            </td>
                        @endforeach
                        <td class="border border-gray-300 p-3 text-center font-semibold text-blue-700 bg-blue-50">
                            {{ $row['total'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($statuses) + 2 }}"
                            class="border border-gray-300 p-4 text-center text-gray-500">
                            Tidak ada data ditemukan.
                        </td>
                    </tr>
                @endforelse

                <!-- Total Row -->
                @if (!empty($totalRow))
                    <tr class="font-bold bg-blue-100 text-blue-800">
                        <td class="border border-blue-600 p-3 text-white bg-blue-700 text-center">TOTAL</td>
                        @foreach ($statuses as $status)
                            <td class="border border-blue-600 p-3 text-center">
                                {{ $totalRow[$status] > 0 ? $totalRow[$status] : '0' }}
                            </td>
                        @endforeach
                        <td class="border border-blue-600 p-3 text-center">{{ $grandTotal }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <script>
        // Ambil periode dari PHP untuk nama file
        const selectedPeriod = "{{ $selectedPeriod }}"; // Format: YYYY-MM
        const formattedPeriod = selectedPeriod.replace('-', '_'); // Ganti - jadi _
        const fileName = `Status_NCX_${formattedPeriod}`;

        function exportToExcel() {
            const table = document.querySelector('table');
            if (!table) {
                alert('Tabel tidak ditemukan!');
                return;
            }

            // Gunakan SheetJS untuk konversi tabel ke workbook
            const wb = XLSX.utils.table_to_book(table, {
                sheet: "Data"
            });

            // Download sebagai file Excel
            XLSX.writeFile(wb, `${fileName}.xlsx`);
        }

        function exportToJPEG() {
            const tableContainer = document.querySelector('.overflow-x-auto');
            if (!tableContainer) {
                alert('Container tabel tidak ditemukan!');
                return;
            }

            // Ambil elemen tabel saja (bukan seluruh container)
            const table = tableContainer.querySelector('table');
            if (!table) return;

            // Tambahkan gaya untuk tampilan print (hitam putih, tanpa hover)
            const tempStyle = document.createElement('style');
            tempStyle.innerHTML = `
            table { background: white !important; }
            td, th { background: white !important; color: black !important; }
            .hover\\:bg-gray-50:hover { background: white !important; }
            .bg-blue-100, .bg-blue-700, .bg-orange-500 { background: white !important; color: black !important; }
            .text-blue-700, .text-white { color: black !important; }
            .bg-blue-50 { background: #e0f2fe !important; }
        `;
            document.head.appendChild(tempStyle);

            // Gunakan html2canvas
            html2canvas(table, {
                backgroundColor: '#ffffff',
                scale: 2, // Resolusi tinggi
                useCORS: true,
                logging: false
            }).then(canvas => {
                // Hapus style sementara
                document.head.removeChild(tempStyle);

                // Konversi ke data URL
                const dataUrl = canvas.toDataURL('image/jpeg', 0.95);

                // Buat link download
                const a = document.createElement('a');
                a.href = dataUrl;
                a.download = `${fileName}.jpg`;
                a.click();
            }).catch(err => {
                console.error('Gagal ekspor ke JPEG:', err);
                alert('Gagal mengekspor ke gambar. Coba lagi.');
            });
        }
    </script>

@endsection
