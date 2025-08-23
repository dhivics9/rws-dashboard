<?php

namespace App\Http\Controllers;

use App\Models\RevenueApi;
use App\Models\TargetsOgd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RevenueAnalyticsController extends Controller
{


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

        try {
            // Kosongkan tabel lama
            RevenueApi::query()->delete();
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
