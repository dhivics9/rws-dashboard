<?php

namespace App\Http\Controllers;

use App\Models\NcxApi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NcxController extends Controller
{
    public function ncxStatus(Request $request)
    {
        $now = Carbon::now();

        // Parse periode dari input (format YYYY-MM)
        $periode = $request->query('periode');
        if ($periode) {
            [$year, $month] = explode('-', $periode);
            $year = (int)$year;
            $month = (int)$month;
        } else {
            $year = $now->year;
            $month = $now->month;
        }

        // Validasi
        if ($month < 1 || $month > 12) {
            $month = $now->month;
        }

        $selectedPeriod = sprintf('%04d-%02d', $year, $month);

        // Ambil filter
        $witelFilter = $request->query('witel') ? explode(',', $request->query('witel')) : [];
        $productFilter = $request->query('product') ? explode(',', $request->query('product')) : [];

        try {
            // Query utama: filter berdasarkan periode dan filter lain
            $query = NcxApi::select([
                'li_product_name',
                'li_milestone',
                'order_created_date',
                'sa_witel' // pastikan kolom ini ada di model
            ])
            ->whereYear('order_created_date', $year)
            ->whereMonth('order_created_date', $month);

            if (!empty($witelFilter)) {
                $query->whereIn('sa_witel', $witelFilter);
            }

            if (!empty($productFilter)) {
                $query->whereIn('li_product_name', $productFilter);
            }

            $results = $query->get();

            // Status yang diharapkan
            $statuses = [
                'TSQ In Progress',
                'TSQ Failed',
                'Provisioning Start',
                'Provisioning Completed',
                'Provisioning Failed',
                'BASO Started',
                'Billing Approval Started',
                'Fulfill Billing Start',
                'Fulfill Billing Completed',
                'Abandoned',
                'Revision In Progress',
                'Cancelled'
            ];

            // Agregasi per layanan
            $data = $results
                ->groupBy('li_product_name')
                ->map(function ($group) use ($statuses) {
                    $summary = array_fill_keys($statuses, 0);
                    $total = 0;

                    foreach ($group as $item) {
                        $milestone = strtolower(trim($item->li_milestone ?? ''));
                        $status = $this->mapMilestoneToStatus($milestone);
                        if (isset($summary[$status])) {
                            $summary[$status]++;
                        }
                        $total++;
                    }

                    return [
                        'layanan' => $group->first()->li_product_name,
                        'counts' => $summary,
                        'total' => $total,
                    ];
                })
                ->sortBy('layanan')
                ->values()
                ->toArray();

            // Hitung total keseluruhan
            $totalRow = array_fill_keys($statuses, 0);
            $grandTotal = 0;
            foreach ($data as $row) {
                foreach ($statuses as $status) {
                    $totalRow[$status] += $row['counts'][$status];
                }
                $grandTotal += $row['total'];
            }

            // Ambil opsi filter dari database (hanya data yang sesuai periode dan filter)
            $witelOptions = NcxApi::whereNotNull('sa_witel')
                ->whereYear('order_created_date', $year)
                ->whereMonth('order_created_date', $month)
                ->distinct()
                ->orderBy('sa_witel')
                ->pluck('sa_witel');

            $productOptions = NcxApi::whereNotNull('li_product_name')
                ->whereYear('order_created_date', $year)
                ->whereMonth('order_created_date', $month)
                ->distinct()
                ->orderBy('li_product_name')
                ->pluck('li_product_name');

            return view('ncx.ncx_status', compact(
                'data',
                'totalRow',
                'grandTotal',
                'statuses',
                'selectedPeriod',
                'witelOptions',
                'productOptions',
                'witelFilter',
                'productFilter'
            ));

        } catch (\Exception $err) {
            return view('ncx.ncx_status', [
                'data' => [],
                'totalRow' => array_fill_keys($statuses, 0),
                'grandTotal' => 0,
                'statuses' => $statuses,
                'selectedPeriod' => $selectedPeriod,
                'witelOptions' => collect(),
                'productOptions' => collect(),
                'witelFilter' => [],
                'productFilter' => []
            ]);
        }
    }

    private function mapMilestoneToStatus(string $milestone): string
    {
        if (empty($milestone)) return 'TSQ In Progress';

        if (str_contains($milestone, 'tsq') || str_contains($milestone, 'survey')) {
            return (str_contains($milestone, 'fail') || str_contains($milestone, 'reject') || str_contains($milestone, 'error'))
                ? 'TSQ Failed' : 'TSQ In Progress';
        }

        if (str_contains($milestone, 'provision')) {
            if (str_contains($milestone, 'complete') || str_contains($milestone, 'success')) {
                return 'Provisioning Completed';
            }
            if (str_contains($milestone, 'fail') || str_contains($milestone, 'error')) {
                return 'Provisioning Failed';
            }
            return 'Provisioning Start';
        }

        if (str_contains($milestone, 'baso')) {
            return 'BASO Started';
        }

        if (str_contains($milestone, 'billing')) {
            if (str_contains($milestone, 'complete') || str_contains($milestone, 'fulfill')) {
                return 'Fulfill Billing Completed';
            }
            if (str_contains($milestone, 'approval')) {
                return 'Billing Approval Started';
            }
            return 'Fulfill Billing Start';
        }

        if (str_contains($milestone, 'abandon')) {
            return 'Abandoned';
        }

        if (str_contains($milestone, 'cancel')) {
            return 'Cancelled';
        }

        if (str_contains($milestone, 'revision') || str_contains($milestone, 'review')) {
            return 'Revision In Progress';
        }

        return 'TSQ In Progress';
    }
}
