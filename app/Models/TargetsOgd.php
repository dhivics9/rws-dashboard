<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetsOgd extends Model
{
    // Nama tabel di database
    protected $table = 'targets_ogd';

    // Primary key
    protected $primaryKey = 'id';

    // Nonaktifkan timestamps (karena tidak ada created_at / updated_at)
    public $timestamps = false;

    // Kolom yang tidak boleh diisi secara massal (blacklist)
    protected $guarded = ['id'];

    // Casting tipe data
    protected $casts = [
        'periode' => 'integer',
        'target' => 'decimal:2',
        'revenue' => 'decimal:2',
        'target_rapp' => 'decimal:2',
    ];
}
