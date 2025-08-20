<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentDetail extends Model
{
    protected $table = 'document_details';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = ['id'];

    // Relasi ke Document
    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id', 'id');
    }

    // Relasi ke Berita Acara
    public function beritaAcara()
    {
        return $this->hasOne(DetailsBeritaAcara::class, 'document_detail_id', 'id');
    }

    // Relasi ke Resign Letter
    public function resignLetter()
    {
        return $this->hasOne(DetailsResignLetter::class, 'document_detail_id', 'id');
    }
}
