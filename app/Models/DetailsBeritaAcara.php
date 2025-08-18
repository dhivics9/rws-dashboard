<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailsBeritaAcara extends Model
{
    // Nama tabel
    protected $table = 'details_berita_acara';

    // Primary key
    protected $primaryKey = 'id'; // ← ganti dari 'detail'

    // Nonaktifkan timestamps
    public $timestamps = false;

    // Kolom yang DILARANG diisi secara massal
    protected $guarded = [
        'detail' // Cegah pengisian primary key
    ];

    // Optional: casting untuk tanggal
    protected $casts = [
        'tanggal_mulai' => 'date',
    ];
}
