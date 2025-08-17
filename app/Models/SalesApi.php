<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesApi extends Model
{
    // Nama tabel di database
    protected $table = 'sales_api';

    // Primary key
    protected $primaryKey = 'id';

    // Nonaktifkan timestamps (karena tidak ada created_at / updated_at)
    public $timestamps = false;

    // Kolom yang tidak boleh diisi secara massal (blacklist)
    protected $guarded = ['id'];

    // Casting tipe data
    protected $casts = [
        'periode' => 'integer',
        'sales_amount' => 'decimal:2', // untuk numeric(19,2)
    ];
}
