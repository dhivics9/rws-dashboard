<?php

namespace App\Http\Controllers;

use App\Models\NcxApi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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

    public function getImport()
    {
        return view('ncx.import');
    }

    public function postImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240'
        ]);

        DB::beginTransaction();
        try {
            // Kosongkan tabel lama
            NcxApi::query()->delete();

            $file = $request->file('file');
            $importedCount = 0;

            // Baca isi Excel
            $rows = Excel::toArray([], $file);
            $dataRows = $rows[0]; // Ambil sheet pertama

            // Buang header (baris pertama)
            array_shift($dataRows);

            foreach ($dataRows as $row) {
                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                // Mapping data dari Excel ke kolom database
                $data = [
                    'li_product_name'             => $this->cleanData($row[0] ?? ''),
                    'ca_account_name'             => $this->cleanData($row[1] ?? ''),
                    'order_id'                    => $this->cleanData($row[2] ?? ''),
                    'li_sid'                      => $this->cleanData($row[3] ?? ''),
                    'quote_subtype'               => $this->cleanData($row[4] ?? ''),
                    'sa_x_addr_city'              => $this->cleanData($row[5] ?? ''),
                    'sa_x_addr_latitude'          => $this->parseDouble($row[6] ?? null),
                    'sa_x_addr_latitude2'         => $this->parseDouble($row[7] ?? null),
                    'sa_x_addr_longlitude'        => $this->parseDouble($row[8] ?? null), // typo: longlitude
                    'sa_x_addr_longlitude2'       => $this->parseDouble($row[9] ?? null),
                    'billing_type_cd'             => $this->cleanData($row[10] ?? ''),
                    'price_type_cd'               => $this->cleanData($row[11] ?? ''),
                    'x_mrc_tot_net_pri'           => $this->parseNumber($row[12] ?? 0),
                    'x_nrc_tot_net_pri'           => $this->parseNumber($row[13] ?? 0),
                    'quote_createdby_name'        => $this->cleanData($row[14] ?? ''),
                    'agree_num'                   => $this->cleanData($row[15] ?? ''),
                    'agree_type'                  => $this->cleanData($row[16] ?? ''),
                    'agree_end_date'              => $this->parseDateTime($row[17] ?? null),
                    'agree_status'                => $this->cleanData($row[18] ?? ''),
                    'li_milestone'                => $this->cleanData($row[19] ?? ''),
                    'order_created_date'          => $this->parseDateTime($row[20] ?? null),
                    'sa_witel'                    => $this->cleanData($row[21] ?? ''),
                    'sa_account_status'           => $this->cleanData($row[22] ?? ''),
                    'sa_account_address_name'     => $this->cleanData($row[23] ?? ''),
                    'billing_activation_date'     => $this->parseDateTime($row[24] ?? null),
                    'billing_activation_status'   => $this->cleanData($row[25] ?? ''),
                    'billcomp_date'               => $this->parseDateTime($row[26] ?? null),
                    'li_milestone_date'           => $this->parseDateTime($row[27] ?? null),
                    'witel'                       => $this->cleanData($row[28] ?? ''),
                    'bw'                          => $this->cleanData($row[29] ?? ''),
                ];

                NcxApi::create($data);
                $importedCount++;
            }

            DB::commit();
            return back()->with('success', "Berhasil mengimport $importedCount data ke tabel ncx_api!");
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

    private function parseDouble($value)
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return null;
        }
        return (float)$value;
    }

    private function parseDateTime($value)
    {
        if (!$value) {
            return null;
        }

        try {
            // Jika string, coba parse sebagai ISO 8601
            $date = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $value);
            if ($date) {
                return $date->format('Y-m-d H:i:s');
            }

            // Atau coba format lain
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
            if ($date) {
                return $date->format('Y-m-d H:i:s');
            }

            // Jika gagal, coba lewat Carbon
            return now()->parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
}
