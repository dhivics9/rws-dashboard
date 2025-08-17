<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NcxApi extends Model
{
    // Nama tabel di database
    protected $table = 'ncx_api';

    // Primary key
    protected $primaryKey = 'id';

    // Nonaktifkan timestamps (karena tidak ada created_at/updated_at)
    public $timestamps = false;

    // Kolom yang TIDAK boleh diisi secara massal (gunakan guarded)
    protected $guarded = ['id'];

    // Casting tipe data agar lebih mudah diproses
    protected $casts = [
        'sa_x_addr_latitude'       => 'double',
        'sa_x_addr_latitude2'      => 'double',
        'sa_x_addr_longlitude'     => 'double',  // Perhatikan: typo di DB (longlitude → longitude)
        'sa_x_addr_longlitude2'    => 'double',
        'x_mrc_tot_net_pri'        => 'decimal:2',
        'x_nrc_tot_net_pri'        => 'decimal:2',
        'agree_end_date'           => 'datetime',
        'order_created_date'       => 'datetime',
        'billing_activation_date'  => 'datetime',
        'billcomp_date'            => 'datetime',
        'li_milestone_date'        => 'datetime',
    ];

    // Jika kamu ingin mengakses kolom dengan nama panjang, kamu bisa tambahkan accessor
    // Contoh: $model->latitude, bukan $model->sa_x_addr_latitude
    // Tapi opsional, tergantung kebutuhan.
}
