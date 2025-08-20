<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailsBeritaAcara extends Model
{
    protected $table = 'details_berita_acara';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = ['id'];

    // Relasi ke DocumentDetail
    public function documentDetail()
    {
        return $this->belongsTo(DocumentDetail::class, 'document_detail_id', 'id');
    }
}
