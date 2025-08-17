@extends('template.conf')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Executive Summary</h1>

    <!-- Statistic Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 mr-4">
                <i class="material-icons text-sm">trending_up</i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Revenue (YTD)</p>
                <p class="text-xl font-bold text-gray-800">Rp.</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $kpi['totalRevenue'] }}</p>
            </div>
        </div>

        <!-- Total Target -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 mr-4">
                <i class="material-icons text-sm">flag</i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Target (YTD)</p>
                <p class="text-xl font-bold text-gray-800">Rp.</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $kpi['totalTarget'] }}</p>
            </div>
        </div>

        <!-- Overall Achievement -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 mr-4">
                <i class="material-icons text-sm">military_tech</i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Overall Achievement (YTD)</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $kpi['activeCustomers'] }}%</p>
            </div>
        </div>

        <!-- Active Customers -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 mr-4">
                <i class="material-icons text-sm">groups</i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Active Customers</p>
                <p class="text-2xl font-semibold text-gray-800">{{ $kpi['achievement'] }}</p>
            </div>
        </div>
    </div>

    {{-- line chart --}}
    <div class="bg-white my-2 rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl text-center font-bold  text-gray-800 mb-4">Revenue Trend (This Year)</h2>
        <div class="chart-container h-80 w-full">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart');

            // Ambil data dari Blade (dikonversi ke JSON)
            const trendData = @json($trend);

            // Ekstrak bulan dan revenue
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const labels = trendData.map(item => months[item.month - 1]); // item.month = 1 → Jan
            const revenueValues = trendData.map(item => parseFloat(item.monthly_revenue) /
                1_000_000_000); // Ubah ke miliar (opsional)

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Monthly Revenue (in Billion IDR)',
                        data: revenueValues,
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderColor: '#10b981',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#10b981',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    // Tampilkan dalam format Rupiah (dalam miliar)
                                    return `Rp ${(context.parsed.y * 1_000_000_000).toLocaleString()} IDR`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return `Rp ${value}B`; // Billion
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>

    {{-- bar chart --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Bar Chart (Left Column) --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Top Region Revenue</h2>
            <div class="h-80 w-full">
                <canvas id="revenueBarChart"></canvas>
            </div>
        </div>

        {{-- Documents (Right Column) --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Recent Documents</h3>
            <div class="space-y-4">
                @foreach ($recentDocuments as $item)
                    <div class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="bg-blue-100 p-2 rounded-lg mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item['file_name']}}</p>
                            <p class="text-xs text-gray-500">{{ $item['upload_timestamp'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueBarChart');

            // Ambil data dari Blade
            const topRegionals = @json($topRegionals);

            // Mapping kode regional ke nama kota
            const regionalNames = {
                '1': 'Jakarta',
                '2': 'Bandung',
                '3': 'Surabaya',
                '4': 'Medan',
                '5': 'Bali',
                '6': 'Makassar',
                '7': 'Semarang',
                '8': 'Malang',
                '9': 'Palembang',
                '10': 'Manado'
                // Tambahkan sesuai kebutuhan
            };

            // Ekstrak labels dan data
            const labels = topRegionals.map(item => regionalNames[item.regional] || `Regional ${item.regional}`);
            const revenueValues = topRegionals.map(item => parseFloat(item.total_revenue) /
            1_000_000_000); // Ubah ke miliar

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue (in Billion IDR)',
                        data: revenueValues,
                        backgroundColor: 'rgba(65, 184, 131, 0.7)',
                        borderColor: 'rgba(65, 184, 131, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed.y * 1_000_000_000;
                                    return 'Revenue: Rp ' + value.toLocaleString('id-ID') + ' IDR';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value + 'B'; // Billion
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
