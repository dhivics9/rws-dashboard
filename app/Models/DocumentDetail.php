<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentDetail extends Model
{
    // Nama tabel di database
    protected $table = 'document_details';

    // Primary key
    protected $primaryKey = 'id';

    // Nonaktifkan timestamps (karena tidak ada created_at / updated_at)
    public $timestamps = false;

    // Kolom yang tidak boleh diisi secara massal (blacklist)
    protected $guarded = ['id'];

    // Casting tipe data
    protected $casts = [
        'document' => 'integer',
    ];

    // Relasi ke tabel documents (asumsi ada tabel documents dengan primary key id)
    // app/Models/DocumentDetail.php
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function beritaAcara()
    {
        return $this->hasOne(DetailsBeritaAcara::class);
    }

   public function resignLetter()
    {
        return $this->hasOne(DetailsResignLetter::class, 'document_detail_id', 'id');
    }
}
