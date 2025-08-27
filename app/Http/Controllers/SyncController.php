<?php

namespace App\Http\Controllers;

use App\Models\NcxApi;
use App\Models\RevenueApi;
use App\Models\SalesApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    private $loginUrl;
    private $user;
    private $pass;
    private $revenueUrl;
    private $ncxUrl;
    private $salesUrl;

    public function __construct()
    {
        $this->loginUrl = env('CENTRAL_API_LOGIN_URL');
        $this->user = env('CENTRAL_API_USER');
        $this->pass = env('CENTRAL_API_PASS');
        $this->revenueUrl = env('CENTRAL_API_REVENUE_URL');
        $this->ncxUrl = env('CENTRAL_API_NCX_URL');
        $this->salesUrl = env('CENTRAL_API_SALES_URL');
    }

    public function index()
    {
        return view('sync.index');
    }

    public function sync()
    {
        if (!$this->loginUrl || !$this->user || !$this->pass) {
            return back()->with('error', 'Konfigurasi API belum lengkap. Cek .env file.');
        }

        try {
            // Step 1: Login untuk dapatkan token
            $token = $this->login();
            if (!$token) {
                return back()->with('error', 'Login ke API pusat gagal. Cek kredensial dan koneksi.');
            }
            // Step 2: Sinkronisasi data
            $this->syncRevenue($token);
            $this->syncNcx($token);

            return back()->with('success', 'Sinkronisasi berhasil! Data dari API pusat telah diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal sinkron: ' . $e->getMessage());
        }
    }

    private function login()
    {
        $response = Http::post($this->loginUrl, [
            'st' => 'login',
            'data' => [
                'username' => $this->user,
                'password' => $this->pass
            ]
        ]);

        if ($response->successful() && isset($response['t'])) {
            return $response['t'];
        }

        throw new \Exception('Login gagal: ' . ($response['message'] ?? $response->body()));
    }

    private function syncRevenue($token)
    {
        $response = Http::withHeaders(['auth-token' => $token])
            ->get($this->revenueUrl);

        if (!$response->successful()) {
            throw new \Exception('Gagal ambil data revenue: ' . $response->status());
        }
        $data = $response->json()['d'] ?? [];

        if (!is_array($data)) $data = [];

        DB::beginTransaction();
        try {
            RevenueApi::query()->delete();
            foreach ($data as $item) {
                RevenueApi::create([
                    'periode'             => (int)($item['Periode'] ?? now()->format('Ym')),
                    'cust_order_number'   => $item['Cust_Order_Number'] ?? null,
                    'product_label'       => $item['Product_Label'] ?? null,
                    'customer_name'       => $item['Customer_Name'] ?? null,
                    'product_name'        => $item['Product_Name'] ?? null,
                    'product_group_name'  => $item['Product_Group_Name'] ?? null,
                    'lccd'                => $item['LCCD'] ?? null,
                    'regional'            => $item['Regional'] ?? null,
                    'witel'               => $item['Witel'] ?? null,
                    'rev_type'            => $item['Rev_Type'] ?? null,
                    'revenue'             => (float)($item['Revenue'] ?? 0),
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function syncNcx($token)
    {
        $response = Http::withHeaders(['auth-token' => $token])
            ->get($this->ncxUrl);

        if (!$response->successful()) {
            throw new \Exception('Gagal ambil data NCX: ' . $response->status());
        }

        $data = $response->json()['d'] ?? [];
        if (!is_array($data)) $data = [];

        DB::beginTransaction();
        try {
            NcxApi::query()->delete();
            foreach ($data as $item) {
                NcxApi::create([
                    'li_product_name'             => $item['LI_PRODUCT_NAME'] ?? null,
                    'ca_account_name'             => $item['CA_ACCOUNT_NAME'] ?? null,
                    'order_id'                    => $item['ORDER_ID'] ?? null,
                    'li_sid'                      => $item['LI_SID'] ?? null,
                    'quote_subtype'               => $item['QUOTE_SUBTYPE'] ?? null,
                    'sa_x_addr_city'              => $item['SA_X_ADDR_CITY'] ?? null,
                    'sa_x_addr_latitude'          => (float)($item['SA_X_ADDR_LATITUDE'] ?? null),
                    'sa_x_addr_latitude2'         => (float)($item['SA_X_ADDR_LATITUDE2'] ?? null),
                    'sa_x_addr_longlitude'        => (float)($item['SA_X_ADDR_LONGLITUDE'] ?? $item['SA_X_ADDR_LONGLOITUDE'] ?? null),
                    'sa_x_addr_longlitude2'       => (float)($item['SA_X_ADDR_LONGLITUDE2'] ?? null),
                    'billing_type_cd'             => $item['BILLING_TYPE_CD'] ?? null,
                    'price_type_cd'               => $item['PRICE_TYPE_CD'] ?? null,
                    'x_mrc_tot_net_pri'           => (float)($item['X_MRC_TOT_NET_PRI'] ?? 0),
                    'x_nrc_tot_net_pri'           => (float)($item['X_NRC_TOT_NET_PRI'] ?? 0),
                    'quote_createdby_name'        => $item['QUOTE_CREATEDBY_NAME'] ?? null,
                    'agree_num'                   => $item['AGREE_NUM'] ?? null,
                    'agree_type'                  => $item['AGREE_TYPE'] ?? null,
                    'agree_end_date'              => $this->parseDate($item['AGREE_END_DATE'] ?? null),
                    'agree_status'                => $item['AGREE_STATUS'] ?? null,
                    'li_milestone'                => $item['LI_MILESTONE'] ?? null,
                    'order_created_date'          => $this->parseDate($item['ORDER_CREATED_DATE'] ?? null),
                    'sa_witel'                    => $item['SA_WITEL'] ?? null,
                    'sa_account_status'           => $item['SA_ACCOUNT_STATUS'] ?? null,
                    'sa_account_address_name'     => $item['SA_ACCOUNT_ADDRESS_NAME'] ?? null,
                    'billing_activation_date'     => $this->parseDate($item['BILLING_ACTIVATION_DATE'] ?? null),
                    'billing_activation_status'   => $item['BILLING_ACTIVATION_STATUS'] ?? null,
                    'billcomp_date'               => $this->parseDate($item['BILLCOMP_DATE'] ?? null),
                    'li_milestone_date'           => $this->parseDate($item['LI_MILESTONE_DATE'] ?? null),
                    'witel'                       => $item['WITEL'] ?? null,
                    'bw'                          => $item['BW'] ?? null,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

      private function parseDate($value)
    {
        if (!$value) return null;
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
