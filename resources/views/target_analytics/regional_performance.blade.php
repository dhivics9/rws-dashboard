@extends('template.conf')

@section('content')

    <!-- Header -->
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Revenue Performance – Per Regional (OGD)</h1>

    <div class="mb-6 p-4">
        <label for="periode" class="block text-sm font-medium text-gray-700 mb-1">PERIODE</label>
        <div class="relative inline-block">

            <input type="month" id="periode" value="{{ $selectedPeriod }}"
                class="w-40 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm bg-white cursor-pointer"
                style="appearance: none; -webkit-appearance: none;" />

            <button type="button"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 cursor-pointer"
                onclick="document.getElementById('periode').showPicker()" title="Pilih periode">
                <i class="fas fa-calendar"></i>
            </button>
        </div>
    </div>

    <!-- Title Table -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-gray-800 uppercase tracking-wide" id="report-title">
            Report Revenue – Per Regional (Periode: {{ $selectedPeriod }})
        </h2>
        <div class="flex space-x-2">
            <!-- Ganti bagian button Excel dengan ini -->
            <button onclick="exportToExcel()"
                class="px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition duration-150">Excel</button>
            <button onclick="exportToJPEG()"
                class="px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition duration-150">JPEG</button>
        </div>
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse table-auto">
            <!-- Header -->
            <thead>
                <tr class="bg-dark-blue text-white text-xs uppercase tracking-wider">
                    <th rowspan="2" class="px-4 py-3 border border-dark-blue text-left">Regional</th>
                    <th colspan="4" class="px-4 py-3 border border-dark-blue text-center">MTD
                        {{ $selectedPeriod ?? '2025-08' }}</th>
                    <th colspan="4" class="px-4 py-3 border border-dark-blue text-center">YTD
                        {{ $selectedPeriod ?? '2025-08' }}</th>
                </tr>
                <tr class="bg-dark-blue text-white text-xs uppercase tracking-wider">
                    <th class="px-4 py-2 border border-dark-blue text-center">TGT</th>
                    <th class="px-4 py-2 border border-dark-blue text-center">REAL</th>
                    <th class="px-4 py-2 border border-dark-blue text-center">ACH</th>
                    <th class="px-4 py-2 border border-dark-blue text-center">RANK</th>
                    <th class="px-4 py-2 border border-dark-blue text-center">TGT</th>
                    <th class="px-4 py-2 border border-dark-blue text-center">REAL</th>
                    <th class="px-4 py-2 border border-dark-blue text-center">ACH</th>
                    <th class="px-4 py-2 border border-dark-blue text-center">RANK</th>
                </tr>
            </thead>

            <!-- Body -->
            <tbody class="text-sm">
                @foreach ($results as $item)
                    <tr class="border-b border-light-gray hover:bg-gray-100 transition-colors">
                        <td class="px-4 py-3 bg-light-gray border border-light-gray text-dark-blue font-medium">
                            {{ $item['regional'] }}</td>
                        <td class="px-4 py-3 text-right border border-light-gray">
                            {{ number_format($item['tgt_mtd'], 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right border border-light-gray">
                            {{ number_format($item['real_mtd'], 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right border border-light-gray">{{ number_format($item['ach_mtd'], 2) }}%
                        </td>
                        <td class="px-4 py-3 text-center border border-light-gray">{{ $item['rank_mtd'] }}</td>
                        <td class="px-4 py-3 text-right border border-light-gray">
                            {{ number_format($item['tgt_ytd'], 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right border border-light-gray">
                            {{ number_format($item['real_ytd'], 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right border border-light-gray">{{ number_format($item['ach_ytd'], 2) }}%
                        </td>
                        <td class="px-4 py-3 text-center border border-light-gray">{{ $item['rank_ytd'] }}</td>
                    </tr>
                @endforeach

                <!-- Baris Total -->
                <tr class="bg-light-gray font-medium">
                    <td class="px-4 py-3 border border-light-gray text-dark-blue">TOTAL</td>
                    <td class="px-4 py-3 text-right border border-light-gray">0</td>
                    <td class="px-4 py-3 text-right border border-light-gray">0</td>
                    <td class="px-4 py-3 text-right border border-light-gray">0.00%</td>
                    <td class="px-4 py-3 text-center border border-light-gray">–</td>
                    <td class="px-4 py-3 text-right border border-light-gray">
                        {{ number_format($total['tgt_ytd'], 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right border border-light-gray">
                        {{ number_format($total['real_ytd'], 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right border border-light-gray">{{ number_format($total['ach_ytd'], 2) }}%
                    </td>
                    <td class="px-4 py-3 text-center border border-light-gray">–</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- JavaScript untuk update judul berdasarkan pilihan periode -->
    <script>
        document.getElementById('periode').addEventListener('change', function() {
            const selectedValue = this.value; // format: YYYY-MM
            const [year, month] = selectedValue.split('-');
            const periodeParam = `${year}${month}`; // format: YYYYMM

            // Redirect ke URL dengan parameter periode
            window.location.href = `?periode=${periodeParam}`;
        });

        // Opsional: set title saat load
        document.addEventListener('DOMContentLoaded', function() {
            const selectedValue = "{{ $selectedPeriod }}";
            const [year, month] = selectedValue.split('-');
            const monthNames = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            const monthName = monthNames[parseInt(month) - 1];
            const formattedPeriod = `${monthName} ${year}`;

            document.getElementById('report-title').textContent =
                `Report Revenue – Per Regional (Periode: ${selectedValue})`;
        });
    </script>

    <script>
        document.getElementById('periode').addEventListener('change', function() {
            const selectedValue = this.value; // format: YYYY-MM
            const [year, month] = selectedValue.split('-');
            const periodeParam = `${year}${month}`; // 202508
            window.location.href = `?periode=${periodeParam}`;
        });
    </script>

    <script>
        function exportToExcel() {
            // Ambil data periode untuk nama file
            const selectedValue = "{{ $selectedPeriod }}";
            const [year, month] = selectedValue.split('-');
            const monthNames = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            const monthName = monthNames[parseInt(month) - 1];
            const fileName = `regional_report_${monthName}_${year}.xlsx`;

            // Buat workbook
            const wb = XLSX.utils.book_new();

            // Buat worksheet dari tabel HTML
            const table = document.querySelector('table');
            const ws = XLSX.utils.table_to_sheet(table);

            // Tambahkan worksheet ke workbook
            XLSX.utils.book_append_sheet(wb, ws, "Regional Report");

            // Ekspor ke file Excel
            XLSX.writeFile(wb, fileName);
        }

        // Fungsi untuk ekspor ke JPEG (opsional)
        function exportToJPEG() {
            // Implementasi ekspor ke JPEG bisa menggunakan html2canvas
            console.log("Export to JPEG functionality would go here");
        }

        document.getElementById('periode').addEventListener('change', function() {
            const selectedValue = this.value;
            const [year, month] = selectedValue.split('-');
            const periodeParam = `${year}${month}`;
            window.location.href = `?periode=${periodeParam}`;
        });

        document.addEventListener('DOMContentLoaded', function() {
            const selectedValue = "{{ $selectedPeriod }}";
            const [year, month] = selectedValue.split('-');
            const monthNames = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            const monthName = monthNames[parseInt(month) - 1];
            const formattedPeriod = `${monthName} ${year}`;

            document.getElementById('report-title').textContent =
                `Report Revenue – Per Regional (Periode: ${selectedValue})`;
        });
    </script>

    <script>
        function exportToJPEG() {
            // Ambil elemen tabel beserta container-nya untuk memastikan semua ter-capture
            const element = document.querySelector('.overflow-x-auto');

            // Buat nama file berdasarkan periode
            const selectedValue = "{{ $selectedPeriod }}";
            const [year, month] = selectedValue.split('-');
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const fileName = `regional_report_${monthNames[parseInt(month)-1]}_${year}.jpeg`;

            // Opsi untuk html2canvas
            const options = {
                scale: 2, // Kualitas 2x lebih tinggi
                logging: false,
                useCORS: true,
                scrollX: 0,
                scrollY: -window.scrollY,
                windowWidth: document.documentElement.offsetWidth,
                windowHeight: document.documentElement.offsetHeight
            };

            // Tampilkan loading indicator
            const loading = document.createElement('div');
            loading.style.position = 'fixed';
            loading.style.top = '0';
            loading.style.left = '0';
            loading.style.width = '100%';
            loading.style.height = '100%';
            loading.style.backgroundColor = 'rgba(0,0,0,0.5)';
            loading.style.display = 'flex';
            loading.style.justifyContent = 'center';
            loading.style.alignItems = 'center';
            loading.style.zIndex = '9999';
            loading.innerHTML = '<div style="color:white; font-size:20px;">Generating JPEG...</div>';
            document.body.appendChild(loading);

            // Capture elemen dan download
            html2canvas(element, options).then(canvas => {
                // Konversi canvas ke JPEG dan download
                const link = document.createElement('a');
                link.download = fileName;
                link.href = canvas.toDataURL('image/jpeg', 0.9); // 90% quality
                link.click();

                // Hapus loading indicator
                document.body.removeChild(loading);

                // Notifikasi sukses
                alert('JPEG exported successfully!');
            }).catch(err => {
                console.error('Error generating JPEG:', err);
                document.body.removeChild(loading);
                alert('Failed to export JPEG. Please try again.');
            });
        }
    </script>
@endsection
