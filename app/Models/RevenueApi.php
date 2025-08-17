<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueApi extends Model
{
    // Nama tabel di database
    protected $table = 'revenue_api';

    // Primary key
    protected $primaryKey = 'id';

    // Nonaktifkan timestamps (karena tidak ada created_at / updated_at)
    public $timestamps = false;

    // Kolom yang tidak boleh diisi secara massal (blacklist)
    protected $guarded = ['id'];

    // Casting tipe data
    protected $casts = [
        'periode' => 'integer',
        'revenue' => 'decimal:2', // untuk numeric(19,2)
    ];
}
