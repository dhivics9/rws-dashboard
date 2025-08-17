<?php

namespace App\Http\Controllers;

use App\Models\TargetsOgd;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class TargetAnalyticsController extends Controller
{


    public function regionalPerformance(Request $request)
    {
        // Ambil periode dari query, default: bulan ini (YYYYMM)
        $periode = $request->query('periode', now()->format('Ym')); // Contoh: 202503

        // Validasi format periode
        if (!preg_match('/^\d{6}$/', $periode)) {
            return response()->json([
                'message' => 'Parameter "periode" harus dalam format YYYYMM.'
            ], 400);
        }

        $year = (int) substr($periode, 0, 4);
        $month = (int) substr($periode, 4, 2);

        if ($month < 1 || $month > 12) {
            return response()->json([
                'message' => 'Bulan harus antara 01 dan 12.'
            ], 400);
        }

        $periodeInt = (int) $periode;

        // Ambil data hanya untuk tahun tersebut
        $startOfPeriod = $year * 100 + 1;
        $endOfPeriod = $year * 100 + 12;

        $data = TargetsOgd::where('periode', '>=', $startOfPeriod)
            ->where('periode', '<=', $endOfPeriod)
            ->get();

        if ($data->isEmpty()) {
            $results = collect(); // kosong
        } else {
            $grouped = $data->groupBy('regional');

            $results = $grouped->map(function (Collection $items) use ($periodeInt, $year) {
                $startOfYear = $year * 100 + 1;

                $tgt_mtd = $items->sum(fn($i) => $i->periode == $periodeInt ? $i->target : 0);
                $real_mtd = $items->sum(fn($i) => $i->periode == $periodeInt ? $i->revenue : 0);

                $tgt_ytd = $items->sum(fn($i) => $i->periode >= $startOfYear && $i->periode <= $periodeInt ? $i->target : 0);
                $real_ytd = $items->sum(fn($i) => $i->periode >= $startOfYear && $i->periode <= $periodeInt ? $i->revenue : 0);

                $ach_mtd = $tgt_mtd == 0 ? 0 : ($real_mtd / $tgt_mtd) * 100;
                $ach_ytd = $tgt_ytd == 0 ? 0 : ($real_ytd / $tgt_ytd) * 100;

                return [
                    'regional' => $items->first()->regional,
                    'tgt_mtd'  => round($tgt_mtd, 2),
                    'real_mtd' => round($real_mtd, 2),
                    'ach_mtd'  => round($ach_mtd, 2),
                    'tgt_ytd'  => round($tgt_ytd, 2),
                    'real_ytd' => round($real_ytd, 2),
                    'ach_ytd'  => round($ach_ytd, 2),
                ];
            })->values();

            // Fungsi untuk menghitung ranking dengan tie handling
            $calculateRanks = function ($collection, $key) {
                // Urutkan collection berdasarkan key (descending)
                $sorted = $collection->sortByDesc($key);

                $rank = 1;
                $previousValue = null;
                $previousRank = null;

                return $sorted->map(function ($item) use (&$rank, &$previousValue, &$previousRank, $key) {
                    $currentValue = $item[$key];

                    if ($previousValue === null) {
                        // Item pertama
                        $item['rank_' . $key] = $rank;
                    } else {
                        if ($currentValue == $previousValue) {
                            // Nilai sama dengan sebelumnya, gunakan rank yang sama
                            $item['rank_' . $key] = $previousRank;
                        } else {
                            // Nilai berbeda, gunakan rank saat ini
                            $item['rank_' . $key] = $rank;
                        }
                    }

                    $previousValue = $currentValue;
                    $previousRank = $item['rank_' . $key];
                    $rank++;

                    return $item;
                });
            };

            // Hitung ranking untuk MTD dan YTD
            $rankedMtd = $calculateRanks($results, 'ach_mtd');
            $rankedYtd = $calculateRanks($results, 'ach_ytd');

            // Gabungkan hasil ranking
            $results = $results->map(function ($item) use ($rankedMtd, $rankedYtd) {
                $mtdRank = $rankedMtd->firstWhere('regional', $item['regional'])['rank_ach_mtd'] ?? null;
                $ytdRank = $rankedYtd->firstWhere('regional', $item['regional'])['rank_ach_ytd'] ?? null;

                $item['rank_mtd'] = $mtdRank;
                $item['rank_ytd'] = $ytdRank;

                return $item;
            })->sortBy('regional')->values();
        }

        // Hitung total
        $total = [
            'tgt_ytd' => $results->sum('tgt_ytd'),
            'real_ytd' => $results->sum('real_ytd'),
            'ach_ytd' => $results->sum('tgt_ytd') == 0 ? 0 : ($results->sum('real_ytd') / $results->sum('tgt_ytd')) * 100,
        ];
        $total['ach_ytd'] = round($total['ach_ytd'], 2);

        // Format YYYY-MM untuk view
        $selectedPeriod = sprintf('%04d-%02d', $year, $month);

        // Kirim ke view
        return view('target_analytics.regional_performance', compact('results', 'total', 'selectedPeriod'));
    }

    public function productSummary(Request $request)
    {
        // Ambil periode dari query, atau gunakan bulan ini sebagai default
        $periode = $request->query('periode');

        // Jika tidak ada periode, gunakan bulan dan tahun saat ini (format YYYYMM)
        if (!$periode) {
            $periode = now()->format('Ym'); // Contoh: 202410
        }

        // Validasi: pastikan periode 6 digit angka
        if (!is_string($periode) || strlen($periode) !== 6 || !is_numeric($periode)) {
            return response()->json([
                'message' => 'Parameter "periode" harus dalam format YYYYMM.'
            ], 400);
        }

        // Konversi ke integer untuk query
        $periodeInt = (int)$periode;

        // Ambil filter lainnya
        $regional = $request->query('regional');
        $witel = $request->query('witel');
        $lccd = $request->query('lccd');
        $stream = $request->query('stream');
        $customer_type = $request->query('customer_type');

        try {
            // Bangun query dengan Eloquent
            $query = TargetsOgd::where('periode', $periodeInt);

            if ($regional) {
                $query->where('regional', $regional);
            }
            if ($witel) {
                $query->where('witel', $witel);
            }
            if ($lccd) {
                $query->where('lccd', $lccd);
            }
            if ($stream) {
                $query->where('stream', $stream);
            }
            if ($customer_type) {
                $query->where('customer_type', $customer_type);
            }

            $data = $query
                ->selectRaw('product_name, customer_name, SUM(target) as sum_target, SUM(revenue) as sum_revenue')
                ->groupBy('product_name', 'customer_name')
                ->orderBy('product_name')
                ->orderBy('customer_name')
                ->get();

            // Transformasi data: kelompokkan per produk
            $report = [];

            foreach ($data as $row) {
                $productName = $row->product_name;

                if (!isset($report[$productName])) {
                    $report[$productName] = [
                        'product_name' => $productName,
                        'total_target' => 0,
                        'total_revenue' => 0,
                        'customers' => []
                    ];
                }

                $target = (float) $row->sum_target;
                $revenue = (float) $row->sum_revenue;

                $report[$productName]['customers'][] = [
                    'customer_name' => $row->customer_name,
                    'target' => $target,
                    'revenue' => $revenue
                ];

                $report[$productName]['total_target'] += $target;
                $report[$productName]['total_revenue'] += $revenue;
            }

            // Ubah associative array ke indexed array
            $report = array_values($report);

            // return response()->json([
            //      $report
            // ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data laporan.'
            ], 500);
        }

        $month = (int) substr($periode, 4, 2);
        $year = (int) substr($periode, 0, 4);
        $selectedPeriod = sprintf('%04d-%02d', $year, $month);

        $filterOptions = filterOptions($request);
        return view("target_analytics.product_summary", compact("selectedPeriod", "filterOptions", "report"));
    }

    public function revenueTable(Request $request){
        // Ambil periode dari query, atau gunakan bulan ini sebagai default
        $periode = $request->query('periode');

        // Jika tidak ada periode, gunakan bulan dan tahun saat ini (format YYYYMM)
        if (!$periode) {
            $periode = now()->format('Ym'); // Contoh: 202410
        }

        $month = (int) substr($periode, 4, 2);
        $year = (int) substr($periode, 0, 4);
        $selectedPeriod = sprintf('%04d-%02d', $year, $month);
        $filterOptions = filterOptions($request);

        return view("target_analytics.revenue_table", compact('selectedPeriod', 'filterOptions'));
    }
}
