<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentDetail;
use App\Models\TargetsOgd;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index(Request $request)
    {
        try {
            $currentYear = Carbon::now()->year;
            $selectedRegional = $request->input('regional', '4'); // Default regional 4

            // Ambil data dari database
            $targets = TargetsOgd::where('periode', 'like', $currentYear . '%')
                ->where('regional', $selectedRegional) // ✅ Perbaikan: Hapus 'operator:'
                ->get(); // Mengembalikan Eloquent Collection

            // Cek apakah $targets benar-benar collection
            if ($targets->isEmpty()) {
                $summary = [
                    'total_revenue' => 0,
                    'total_target' => 0,
                    'active_customers' => 0,
                    'achievement' => 0
                ];
                $trend = collect([]);
            } else {
                // Summary Data
                $summary = [
                    'total_revenue' => $targets->sum('revenue'),
                    'total_target' => $targets->sum('target'),
                    'active_customers' => $targets->unique('customer_name')->count(),
                    'achievement' => $targets->sum('target') > 0
                        ? ($targets->sum('revenue') / $targets->sum('target')) * 100
                        : 0
                ];

                // Group by month using Collection's groupBy with closure
                $trend = $targets
                    ->groupBy(fn($item) => substr($item->periode, 4, 2)) // Ambil bulan
                    ->map(function ($group, $month) {
                        return [
                            'month' => (int)$month,
                            'monthly_revenue' => (string)$group->sum('revenue')
                        ];
                    })
                    ->values()
                    ->sortBy('month')
                    ->values();
            }

            // Available regionals for filter
            $availableRegionals = TargetsOgd::where('periode', 'like', $currentYear . '%')
                ->pluck('regional')
                ->unique()
                ->sort()
                ->values();

            // Top 5 Regionals (global, tanpa filter)
            $topRegionalsGlobal = TargetsOgd::where('periode', 'like', $currentYear . '%')
                ->get()
                ->groupBy('regional')
                ->map(function ($group, $regional) {
                    return [
                        'regional' => $regional,
                        'total_revenue' => (string)$group->sum('revenue')
                    ];
                })
                ->sortByDesc('total_revenue')
                ->take(5)
                ->values();

            // Recent Documents
            $recentDocuments = Document::limit(5)->get();

            $kpi = [
                'totalRevenue' => (float)$summary['total_revenue'],
                'totalTarget' => (float)$summary['total_target'],
                'activeCustomers' => (int)$summary['active_customers'],
                'achievement' => round($summary['achievement'], 2)
            ];

            $witelData = $targets->groupBy('witel')
                ->map(function ($group) {
                    return [
                        'witel' => $group->first()->witel,
                        'target' => (float)$group->sum('target'),
                        'revenue' => (float)$group->sum('revenue')
                    ];
                })
                ->values();

            // Urutkan berdasarkan nama witel (opsional)
            $witelData = $targets->groupBy('witel')
                ->map(function ($group) {
                    $witelName = $group->first()->witel ?? 'Tidak Diketahui'; // Jika null, jadi "Tidak Diketahui"
                    return [
                        'witel' => $witelName,
                        'target' => (float)$group->sum('target'),
                        'revenue' => (float)$group->sum('revenue')
                    ];
                })
                ->sortByDesc('revenue') // Urutkan berdasarkan revenue turun
                ->take(10)               // Ambil 10 teratas
                ->values();
            $maxValue = $witelData->max(fn($item) => max($item['target'], $item['revenue']));

            // dd($maxValue);

            return view('home', compact(
                'kpi',
                'witelData',
                'trend',
                'topRegionalsGlobal',
                'recentDocuments',
                'selectedRegional',
                'availableRegionals',
                'maxValue'
            ));
        } catch (\Exception $err) {
            // Untuk debugging, kamu bisa log error
            return response()->json(['error' => $err->getMessage()], 500);
        }
    }

    //     return view('home');
    // }
}
