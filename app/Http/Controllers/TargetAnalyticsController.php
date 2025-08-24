<?php

namespace App\Http\Controllers;

use App\Models\TargetsOgd;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Facades\Excel;

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
        // Hitung total
        $total = [
            'tgt_mtd'  => $results->sum('tgt_mtd'),
            'real_mtd' => $results->sum('real_mtd'),
            'ach_mtd'  => $results->sum('tgt_mtd') == 0 ? 0 : ($results->sum('real_mtd') / $results->sum('tgt_mtd')) * 100,

            'tgt_ytd'  => $results->sum('tgt_ytd'),
            'real_ytd' => $results->sum('real_ytd'),
            'ach_ytd'  => $results->sum('tgt_ytd') == 0 ? 0 : ($results->sum('real_ytd') / $results->sum('tgt_ytd')) * 100,
        ];

        // Bulatkan ke 2 angka desimal
        $total['ach_mtd'] = round($total['ach_mtd'], 2);
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

    public function revenueTable(Request $request)
    {
        // Default periode: bulan ini (YYYYMM)
        $periode = $request->query('periode') ?? now()->format('Ym');
        $month = (int) substr($periode, 4, 2);
        $year = (int) substr($periode, 0, 4);
        $selectedPeriod = sprintf('%04d-%02d', $year, $month);
        $filterOptions = filterOptions($request);

        // Helper: parse CSV
        $parseCsv = function ($value) {
            if (empty($value)) return [];
            return collect(explode(',', $value))
                ->map(fn($v) => trim($v))
                ->filter()
                ->values()
                ->all();
        };

        $ignoreValues = ['All', 'All Regionals', 'All Witels', 'All LCCDs', 'All Streams', 'All Customer Types'];

        // Bangun query
        $query = TargetsOgd::query();

        // Filter text fields
        foreach (['regional', 'witel', 'lccd', 'stream', 'customer_type'] as $field) {
            $raw = $request->query($field);
            $values = $parseCsv($raw);
            $filtered = array_filter($values, fn($v) => !in_array($v, $ignoreValues));
            if (!empty($filtered)) {
                $query->whereIn($field, $filtered);
            }
        }

        // Filter year
        $years = $parseCsv($request->query('year', ''));
        $validYears = array_filter($years, fn($y) => $y !== 'All' && preg_match('/^\d{4}$/', $y));
        if (!empty($validYears)) {
            $query->where(function ($q) use ($validYears) {
                foreach ($validYears as $y) {
                    $q->orWhereRaw('SUBSTRING(CAST(periode AS TEXT), 1, 4) = ?', [$y]);
                }
            });
        }

        // Filter month
        $months = $parseCsv($request->query('month', ''));
        $validMonths = array_filter($months, fn($m) => $m !== 'All' && preg_match('/^\d{2}$/', $m));
        if (!empty($validMonths)) {
            $query->where(function ($q) use ($validMonths) {
                foreach ($validMonths as $m) {
                    $q->orWhereRaw('SUBSTRING(CAST(periode AS TEXT), 5, 2) = ?', [$m]);
                }
            });
        }

        // Gunakan paginate() langsung → ini menghasilkan LengthAwarePaginator
        $tableData = $query->orderBy('id', 'DESC')
            ->paginate($request->query('limit', 10))
            ->appends($request->except('page')); // Pertahankan semua query kecuali page


        return view('target_analytics.revenue_data', compact('tableData', 'selectedPeriod', 'filterOptions'));
    }

    public function getImport()
    {
        return view('target_analytics.import');
    }

    public function postImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240'
        ]);

        DB::beginTransaction();
        try {
            // Kosongkan tabel lama
            TargetsOgd::query()->delete();

            $file = $request->file('file');
            $importedCount = 0;
            $extension = $file->getClientOriginalExtension();

            if (in_array($extension, ['xlsx', 'xls', 'csv', 'txt'])) {
                // Baca isi excel
                $rows = Excel::toArray([], $file);
                // $rows[0] = sheet pertama
                $dataRows = $rows[0];

                // Buang header (baris pertama)
                array_shift($dataRows);

                foreach ($dataRows as $row) {
                    // Skip baris kosong
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $data = [
                        'regional'      => $this->cleanData($row[0] ?? ''),
                        'witel'         => $this->cleanData($row[1] ?? ''), // Sama dengan regional
                        'lccd'          => $this->cleanData($row[2] ?? ''),
                        'stream'        => $this->cleanData($row[3] ?? ''),
                        'product_name'  => $this->cleanProductName($row[4] ?? ''),
                        'gl_account'    => $this->cleanData($row[5] ?? ''),
                        'bp_number'     => $this->cleanData($row[6] ?? ''),
                        'customer_name' => $this->cleanData($row[7] ?? ''),
                        'customer_type' => $this->cleanData($row[8] ?? ''),
                        'target'        => $this->parseNumber($row[9] ?? 0),
                        'revenue'       => $this->parseNumber($row[10] ?? 0),
                        'periode'       => (int)$this->cleanData($row[11] ?? date('Ym')),
                        'target_rkapp'  => $this->parseNumber($row[12] ?? 0),
                    ];

                    TargetsOgd::create($data);
                    $importedCount++;
                }
            } elseif ($extension === 'json') {
                $jsonContent = file_get_contents($file->getRealPath());
                $jsonData = json_decode($jsonContent, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON: ' . json_last_error_msg());
                }

                if (!is_array($jsonData)) {
                    $jsonData = [$jsonData];
                }

                foreach ($jsonData as $item) {
                    $data = [
                        'regional'      => $this->cleanData($item['regional'] ?? ''),
                        'witel'         => $this->cleanData($item['witel'] ?? ''),
                        'lccd'          => $this->cleanData($item['lccd'] ?? ''),
                        'stream'        => $this->cleanData($item['stream'] ?? ''),
                        'product_name'  => $this->cleanProductName($item['product_name'] ?? ''),
                        'gl_account'    => $this->cleanData($item['gl_account'] ?? ''),
                        'bp_number'     => $this->cleanData($item['bp_number'] ?? ''),
                        'customer_name' => $this->cleanData($item['customer_name'] ?? ''),
                        'customer_type' => $this->cleanData($item['customer_type'] ?? ''),
                        'target'        => $this->parseNumber($item['target'] ?? 0),
                        'revenue'       => $this->parseNumber($item['revenue'] ?? 0),
                        'periode'       => (int)($item['periode'] ?? date('Ym')),
                        'target_rkapp'  => $this->parseNumber($item['target_rkapp'] ?? 0),
                    ];

                    TargetsOgd::create($data);
                    $importedCount++;
                }
            }

            DB::commit();
            return back()->with('success', "Berhasil mengimport $importedCount data!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Helper functions untuk cleaning data
     */
    private function cleanData($value)
    {
        $value = trim($value);
        $value = str_replace(['"', "'"], '', $value); // Hapus tanda kutip
        return $value === '' ? null : $value;
    }

    private function cleanProductName($value)
    {
        $value = $this->cleanData($value);
        $value = str_replace(['[', ']'], '', $value); // Hapus kurung siku
        return $value;
    }

    private function parseNumber($value)
    {
        if (is_numeric($value)) {
            return (float)$value;
        }

        $cleaned = str_replace(['.', ','], ['', '.'], $value);
        $cleaned = preg_replace('/[^0-9.-]/', '', $cleaned);

        return (float)$cleaned;
    }
}
