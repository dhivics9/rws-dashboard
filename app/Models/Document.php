<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    // Nama tabel di database
    protected $table = 'documents';

    // Primary key
    protected $primaryKey = 'id';

    // Nonaktifkan timestamps (karena tidak ada created_at / updated_at)
    public $timestamps = false;

    // Kolom yang tidak boleh diisi secara massal (blacklist)
    protected $guarded = ['id'];

    // Casting tipe data
    protected $casts = [
        'file_size' => 'integer',
        'upload_timestamp' => 'datetime',
    ];
}
