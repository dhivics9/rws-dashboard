<?php

namespace App\Http\Controllers;

use App\Models\RevenueApi;
use App\Models\TargetsOgd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RevenueAnalyticsController extends Controller
{
    public function revenueData(Request $request)
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

        return view('revenue_analytics.revenue_data', compact('tableData', 'selectedPeriod', 'filterOptions'));
    }

    public function getImport()
    {
        return view('revenue_analytics.import');
    }

    public function postImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt'
        ]);

        DB::beginTransaction();
        // dd("kontol");
        try {
            // Kosongkan tabel lama
            RevenueApi::truncate();
            $file = $request->file('file');
            $importedCount = 0;

            // Baca isi excel
            $rows = Excel::toArray([], $file);
            $dataRows = $rows[0]; // Sheet pertama

            // Buang header (baris pertama)
            array_shift($dataRows);

            foreach ($dataRows as $row) {
                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                // Mapping kolom Excel ke database
                $data = [
                    'periode'             => (int)$this->cleanData($row[0] ?? ''),
                    'cust_order_number'   => $this->cleanData($row[1] ?? ''),
                    'product_label'       => $this->cleanData($row[2] ?? ''),
                    'customer_name'       => $this->cleanData($row[3] ?? ''),
                    'product_name'        => $this->cleanData($row[4] ?? ''),
                    'product_group_name'  => $this->cleanData($row[5] ?? ''),
                    'lccd'                => $this->cleanData($row[6] ?? ''),
                    'regional'            => $this->cleanData($row[7] ?? ''),
                    'witel'               => $this->cleanData($row[8] ?? ''),
                    'rev_type'            => $this->cleanData($row[9] ?? ''),
                    'revenue'             => $this->parseNumber($row[10] ?? 0),
                ];

                RevenueApi::create($data);
                $importedCount++;
            }

            DB::commit();
            return back()->with('success', "Berhasil mengimport $importedCount data ke tabel revenue_api!");
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
