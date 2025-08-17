<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailsResignLetter extends Model
{
    // Nama tabel
    protected $table = 'details_resign_letter';

    // Primary key
    protected $primaryKey = 'detail';

    // Nonaktifkan timestamps
    public $timestamps = false;

    // Kolom yang TIDAK boleh diisi massal (gunakan guarded)
    protected $guarded = ['detail'];

    // Casting tipe data
    protected $casts = [
        'last_day_of_work' => 'date',
    ];
}
