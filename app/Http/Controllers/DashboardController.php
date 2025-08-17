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
    public function index()
    {

        try {
            $currentYear = Carbon::now()->year;

            // Summary Data
            $targets = TargetsOgd::where('periode', 'like', $currentYear . '%')->get();

            $summary = [
                'total_revenue' => $targets->sum('revenue'),
                'total_target' => $targets->sum('target'),
                'active_customers' => $targets->unique('customer_name')->count(),
                'achievement' => $targets->sum('target') > 0
                    ? ($targets->sum('revenue') / $targets->sum('target')) * 100
                    : 0
            ];

            // Trend Data: Group by month (extracted from periode like '202401')
            $trend = $targets->groupBy(function ($item) {
                return substr($item->periode, 4, 2); // Ambil bulan: 01, 02, dst
            })->map(function ($monthlyTargets, $month) {
                return [
                    'month' => (int) $month, // Hilangkan leading zero, jadi 1, 2, dst
                    'monthly_revenue' => (string) $monthlyTargets->sum('revenue') // Pastikan string untuk presisi
                ];
            })->values()->sortBy('month')->values(); // Urutkan berdasarkan bulan dan reset key

            // Top Regionals: Group by regional
            $topRegionals = $targets->groupBy('regional')
                ->map(function ($regionalTargets, $regional) {
                    return [
                        'regional' => $regional,
                        'total_revenue' => (string) $regionalTargets->sum('revenue')
                    ];
                })
                ->sortByDesc('total_revenue')
                ->take(5)
                ->values(); // Reset key agar jadi array numerik

            // Recent Documents
            $recentDocuments = Document::limit(5)->get();

            $kpi = [
                'totalRevenue' => (float) $summary['total_revenue'],
                'totalTarget' => (float) $summary['total_target'],
                'activeCustomers' => (int) $summary['active_customers'],
                'achievement' => round($summary['achievement'], 2)
            ];

            return view('home', compact('kpi', 'trend', 'topRegionals', 'recentDocuments'));
        } catch (\Exception $err) {
            return response()->json(['error' => $err->getMessage()], 500);
        }
    }

    //     return view('home');
    // }
}
