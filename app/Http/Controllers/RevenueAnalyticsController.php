<?php

namespace App\Http\Controllers;

use App\Models\TargetsOgd;
use Illuminate\Http\Request;

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

        return view('revenue-analytics.revenue_data', compact('tableData', 'selectedPeriod', 'filterOptions'));
    }
}
