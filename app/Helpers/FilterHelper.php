<?php

// app/Helpers/FilterHelper.php

use App\Models\TargetsOgd;
use Illuminate\Http\Request;

if (!function_exists('filterOptions')) {
    function filterOptions(Request $request)
    {
        // Default query parameters
        $regional = $request->input('regional', '');
        $witel = $request->input('witel', '');
        $lccd = $request->input('lccd', '');
        $stream = $request->input('stream', '');
        $customerType = $request->input('customer_type', '');
        $year = $request->input('year', '');
        $month = $request->input('month', '');

        // Helper: parse CSV-like string to array
        $parseCsv = function ($value) {
            if (!$value) return [];
            return collect(explode(',', $value))
                ->map(fn($s) => trim($s))
                ->filter()
                ->values()
                ->all();
        };

        // Base query
        $query = TargetsOgd::query();

        // Filter functions
        $addInFilter = function ($query, $column, $rawValues, $ignore = ['All', 'All Regionals']) use ($parseCsv) {
            $values = $parseCsv($rawValues);
            $filtered = array_values(array_filter($values, fn($v) => !in_array($v, $ignore)));
            if (empty($filtered)) return $query;
            return $query->whereIn($column, $filtered);
        };

        $query = $addInFilter($query, 'regional', $regional, ['All', 'All Regionals']);
        $query = $addInFilter($query, 'witel', $witel, ['All', 'All Witels']);
        $query = $addInFilter($query, 'lccd', $lccd, ['All', 'All LCCDs']);
        $query = $addInFilter($query, 'stream', $stream, ['All', 'All Streams']);
        $query = $addInFilter($query, 'customer_type', $customerType, ['All', 'All Customer Types']);

        // Filter by year (periode: YYYYMM)
        $years = $parseCsv($year);
        $validYears = array_values(array_filter($years, fn($y) => $y !== 'All' && preg_match('/^\d{4}$/', $y)));
        if (!empty($validYears)) {
            $query->where(function ($q) use ($validYears) {
                foreach ($validYears as $yr) {
                    $q->orWhereRaw('SUBSTRING(periode, 1, 4) = ?', [$yr]);
                }
            });
        }

        // Filter by month (periode: YYYYMM)
        $months = $parseCsv($month);
        $validMonths = array_values(array_filter($months, fn($m) => $m !== 'All' && preg_match('/^\d{1,2}$/', $m)));
        if (!empty($validMonths)) {
            $query->where(function ($q) use ($validMonths) {
                foreach ($validMonths as $mo) {
                    // Format bulan menjadi 2 digit (01, 02, ..., 12)
                    $formattedMonth = str_pad($mo, 2, '0', STR_PAD_LEFT);
                    $q->orWhereRaw('SUBSTRING(periode, 5, 2) = ?', [$formattedMonth]);
                }
            });
        }

        // Get filter options (distinct values)
        $filterFields = [
            'regional' => 'regionals',
            'witel' => 'witels',
            'lccd' => 'lccds',
            'stream' => 'streams',
            'customer_type' => 'customerTypes',
        ];

        $filterOptions = [];
        foreach ($filterFields as $column => $key) {
            $values = TargetsOgd::select($column)
                ->distinct()
                ->orderBy($column)
                ->whereNotNull($column)
                ->where($column, '!=', '')
                ->pluck($column)
                ->filter()
                ->values()
                ->all();

            $filterOptions[$key] = $values;
        }

        // Get unique years from periode
        $yearsFromDb = TargetsOgd::selectRaw('DISTINCT SUBSTRING(periode, 1, 4) as year')
            ->whereNotNull('periode')
            ->where('periode', '!=', '')
            ->orderBy('year')
            ->pluck('year')
            ->filter()
            ->values()
            ->all();

        $filterOptions['years'] = array_merge(['All Years'], $yearsFromDb);

        // Get unique months (optional - jika ingin menampilkan pilihan bulan)
        $monthsFromDb = TargetsOgd::selectRaw('DISTINCT SUBSTRING(periode, 5, 2) as month')
            ->whereNotNull('periode')
            ->where('periode', '!=', '')
            ->orderBy('month')
            ->pluck('month')
            ->filter()
            ->values()
            ->all();

        $filterOptions['months'] = array_merge(['All Months'], $monthsFromDb);

        return $filterOptions;
    }
}
