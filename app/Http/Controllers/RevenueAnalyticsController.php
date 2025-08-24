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
            'file' => 'required|file|mimes:xlsx,xls,csv,txt,json|max:10240' // tambah json
        ]);

        DB::beginTransaction();
        try {
            // Kosongkan tabel lama
            RevenueApi::query()->delete();

            $file = $request->file('file');
            $importedCount = 0;

            $extension = $file->getClientOriginalExtension();

            if (in_array($extension, ['xlsx', 'xls', 'csv', 'txt'])) {
                // Proses Excel/CSV
                $rows = Excel::toArray([], $file);
                $dataRows = $rows[0]; // Sheet pertama
                array_shift($dataRows); // buang header

                foreach ($dataRows as $row) {
                    if (empty(array_filter($row))) continue;

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
            } elseif ($extension === 'json') {
                // Proses JSON
                $jsonContent = file_get_contents($file->getRealPath());
                $jsonData = json_decode($jsonContent, true);

                // Jika JSON tidak valid
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON: ' . json_last_error_msg());
                }

                // Pastikan data adalah array
                if (!is_array($jsonData)) {
                    $jsonData = [$jsonData]; // jika objek tunggal, jadikan array
                }

                foreach ($jsonData as $item) {
                    // if (!isset($item['Periode'])) continue; // skip jika tidak valid

                    $data = [
                        'periode'             => (int)($item['Periode'] ?? ''),
                        'cust_order_number'   => $this->cleanData($item['Cust_Order_Number'] ?? null),
                        'product_label'       => $this->cleanData($item['Product_Label'] ?? null),
                        'customer_name'       => $this->cleanData($item['Customer_Name'] ?? null),
                        'product_name'        => $this->cleanData($item['Product_Name'] ?? null),
                        'product_group_name'  => $this->cleanData($item['Product_Group_Name'] ?? null),
                        'lccd'                => $this->cleanData($item['LCCD'] ?? null),
                        'regional'            => $this->cleanData($item['Regional'] ?? null),
                        'witel'               => $this->cleanData($item['Witel'] ?? null),
                        'rev_type'            => $this->cleanData($item['Rev_Type'] ?? null),
                        'revenue'             => $this->parseNumber($item['Revenue'] ?? 0),
                    ];

                    RevenueApi::create($data);
                    $importedCount++;
                }
            } else {
                throw new \Exception("Format file tidak didukung: $extension");
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
