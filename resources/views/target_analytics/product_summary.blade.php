@extends('template.conf')

@section('content')
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Product & Customer Summary Report
    </h1>

    <!-- Title Table -->
    <div class="flex justify-between items-center mb-6">
        @include('atoms.filter')
        <div class="flex space-x-2">
            <button onclick="exportToExcel()"
                class="px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition duration-150">Excel</button>
            <button onclick="exportToJPEG()"
                class="px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md transition duration-150">JPEG</button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="product-summary-table">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Product Name
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Customer Name
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Target
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Revenue
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Achievement (%)
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $grandTotalTarget = 0;
                    $grandTotalRevenue = 0;
                @endphp

                @forelse ($report as $product)
                    @php
                        $slug = \Illuminate\Support\Str::slug($product['product_name']);
                        $productId = 'product-' . $loop->index . '-' . $slug; // Unique ID
                        $achievement =
                            $product['total_target'] > 0
                                ? ($product['total_revenue'] / $product['total_target']) * 100
                                : 0;

                        $achievementColor =
                            $achievement >= 100
                                ? 'text-green-600'
                                : ($achievement >= 80
                                    ? 'text-yellow-600'
                                    : 'text-red-600');

                        // Add to grand totals
                        $grandTotalTarget += $product['total_target'];
                        $grandTotalRevenue += $product['total_revenue'];
                    @endphp

                    <!-- Baris Produk (Expandable) -->
                    <tr class="bg-blue-50 hover:bg-blue-100 cursor-pointer" onclick="toggleProduct('{{ $productId }}')">
                        <td class="px-6 py-4 whitespace-nowrap font-medium">
                            <div class="flex items-center">
                                <svg id="icon-{{ $productId }}"
                                    class="w-4 h-4 mr-1 text-gray-700 transition-transform duration-200" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                                {{ $product['product_name'] }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ count($product['customers']) }} customer{{ count($product['customers']) != 1 ? 's' : '' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium">
                            Rp {{ number_format($product['total_target'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium">
                            Rp {{ number_format($product['total_revenue'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold {{ $achievementColor }}">
                            {{ number_format($achievement, 2) }}%
                        </td>
                    </tr>

                    <!-- Detail Customers -->
                    @foreach ($product['customers'] as $customer)
                        @php
                            $custAchievement =
                                $customer['target'] > 0 ? ($customer['revenue'] / $customer['target']) * 100 : 0;

                            $custAchievementColor =
                                $custAchievement >= 100
                                    ? 'text-green-600'
                                    : ($custAchievement >= 80
                                        ? 'text-yellow-600'
                                        : 'text-red-600');
                        @endphp
                        <tr id="detail-{{ $productId }}" class="hidden bg-blue-50 border-t border-blue-200">
                            <td class="px-6 py-2 whitespace-nowrap"></td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm">{{ $customer['customer_name'] }}</td>
                            <td class="px-6 py-2 whitespace-nowrap">
                                Rp {{ number_format($customer['target'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap">
                                Rp {{ number_format($customer['revenue'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap font-semibold {{ $custAchievementColor }}">
                                {{ number_format($custAchievement, 2) }}%
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data tersedia.</td>
                    </tr>
                @endforelse

                <!-- Grand Total Row -->
                @if(count($report) > 0)
                    @php
                        $grandAchievement = $grandTotalTarget > 0 ? ($grandTotalRevenue / $grandTotalTarget) * 100 : 0;
                        $grandAchievementColor = $grandAchievement >= 100
                            ? 'text-green-600'
                            : ($grandAchievement >= 80
                                ? 'text-yellow-600'
                                : 'text-red-600');
                    @endphp
                    <tr class="bg-gray-100 font-bold">
                        <td class="px-6 py-4 whitespace-nowrap">Grand Total</td>
                        <td class="px-6 py-4 whitespace-nowrap"></td>
                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($grandTotalTarget, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($grandTotalRevenue, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap {{ $grandAchievementColor }}">
                            {{ number_format($grandAchievement, 2) }}%
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <script>
        function toggleProduct(productId) {
            const details = document.querySelectorAll('#detail-' + productId);
            const icon = document.getElementById('icon-' + productId);

            details.forEach(row => {
                if (row.classList.contains('hidden')) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });

            // Rotate icon
            if (icon) {
                icon.classList.toggle('transform');
                icon.classList.toggle('rotate-180');
            }
        }

        // Optional: export functions
        function exportToExcel() {
            // Ambil periode dari judul
            const periodeText = document.getElementById('report-title').textContent;
            const match = periodeText.match(/Periode:\s*(\d{4})-(\d{2})/);
            const year = match ? match[1] : new Date().getFullYear();
            const month = match ? match[2] : String(new Date().getMonth() + 1).padStart(2, '0');
            const periode = year + month;
            const filename = `product_customer_summary_report_${periode}.xlsx`;

            // Buat salinan tabel untuk export (biar bisa dibersihkan)
            const table = document.getElementById('product-summary-table');
            const workbook = XLSX.utils.book_new();
            const worksheetData = [];

            // Tambahkan judul
            worksheetData.push([{
                v: 'Product & Customer Summary Report',
                t: 's',
                s: {
                    font: {
                        sz: 16,
                        bold: true
                    }
                }
            }]);
            worksheetData.push([{
                v: 'Periode: ' + year + '-' + month,
                t: 's',
                s: {
                    font: {
                        italic: true
                    }
                }
            }]);
            worksheetData.push([]); // baris kosong

            // Header tabel
            worksheetData.push([
                'Product Name',
                'Customer Name',
                'Target',
                'Revenue',
                'Achievement (%)'
            ]);

            // Gaya header
            const headerStyle = {
                font: {
                    bold: true
                },
                fill: {
                    fgColor: {
                        rgb: "E2E2E2"
                    }
                }
            };
            const lastHeaderRow = worksheetData.length - 1;

            // Loop semua baris di tabel
            Array.from(table.querySelectorAll('tbody tr')).forEach(tr => {
                // Skip the grand total row for now, we'll add it separately
                if (tr.classList.contains('bg-gray-100')) return;

                const cells = Array.from(tr.children);
                const rowData = [];

                // Ambil teks dari tiap sel, bersihkan dari ikon
                cells.forEach((cell, index) => {
                    let text = cell.textContent.trim();

                    // Kolom Product Name: bersihkan dari ikon dan "expandable"
                    if (index === 0 && tr.classList.contains('bg-blue-50')) {
                        text = text.replace(/^[^\w\s]/, '').trim(); // hapus simbol panah
                    }

                    // Kolom Target & Revenue: bersihkan dari "Rp" dan titik, jadikan angka
                    if ((index === 2 || index === 3) && text) {
                        const num = parseFloat(text.replace(/[^\d.-]/g, '')) || 0;
                        rowData.push({
                            v: num,
                            t: 'n',
                            z: '#,##0'
                        });
                    }
                    // Kolom Achievement: ambil angka persen
                    else if (index === 4 && text.includes('%')) {
                        const num = parseFloat(text.replace(/[^\d.-]/g, '')) || 0;
                        rowData.push({
                            v: num / 100,
                            t: 'n',
                            z: '0.00%'
                        });
                    }
                    // Lainnya: teks biasa
                    else {
                        rowData.push({
                            v: text,
                            t: 's'
                        });
                    }
                });

                worksheetData.push(rowData);
            });

            // Add grand total row
            const grandTotalRow = table.querySelector('tr.bg-gray-100');
            if (grandTotalRow) {
                const cells = Array.from(grandTotalRow.children);
                const rowData = [];

                cells.forEach((cell, index) => {
                    let text = cell.textContent.trim();

                    // Kolom Target & Revenue: bersihkan dari "Rp" dan titik, jadikan angka
                    if ((index === 2 || index === 3) && text) {
                        const num = parseFloat(text.replace(/[^\d.-]/g, '')) || 0;
                        rowData.push({
                            v: num,
                            t: 'n',
                            z: '#,##0'
                        });
                    }
                    // Kolom Achievement: ambil angka persen
                    else if (index === 4 && text.includes('%')) {
                        const num = parseFloat(text.replace(/[^\d.-]/g, '')) || 0;
                        rowData.push({
                            v: num / 100,
                            t: 'n',
                            z: '0.00%'
                        });
                    }
                    // Lainnya: teks biasa
                    else {
                        rowData.push({
                            v: text,
                            t: 's'
                        });
                    }
                });

                worksheetData.push(rowData);

                // Apply bold style to grand total row
                for (let i = 0; i < 5; i++) {
                    const cell = XLSX.utils.encode_cell({
                        r: worksheetData.length - 1,
                        c: i
                    });
                    if (!ws[cell]) ws[cell] = { t: 's' };
                    ws[cell].s = { font: { bold: true } };
                }
            }

            // Buat worksheet
            const ws = XLSX.utils.aoa_to_sheet(worksheetData);

            // Atur lebar kolom
            ws['!cols'] = [{
                    wch: 25
                },
                {
                    wch: 30
                },
                {
                    wch: 15
                },
                {
                    wch: 15
                },
                {
                    wch: 15
                }
            ];

            // Atur gaya header
            for (let i = 0; i < 5; i++) {
                const cell = XLSX.utils.encode_cell({
                    r: lastHeaderRow,
                    c: i
                });
                if (!ws[cell]) ws[cell] = {
                    t: 's'
                };
                ws[cell].s = headerStyle;
            }

            // Tambahkan ke workbook
            XLSX.utils.book_append_sheet(workbook, ws, 'Report');

            // Download
            XLSX.writeFile(workbook, filename);
        }

        function exportToJPEG() {
            alert('Fitur export JPEG akan segera hadir.');
        }
    </script>
@endsection
