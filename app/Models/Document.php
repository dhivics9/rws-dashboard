<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    // Boot untuk generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->slug) {
                $model->slug = $model->generateSlug();
            }
        });

        static::updating(function ($model) {
            if (!$model->slug) {
                $model->slug = $model->generateSlug();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
    // app/Models/Document.php

    protected function generateSlug()
    {
        // Ambil subject utama dari detail
        $detail = $this->documentDetail;

        if (!$detail) {
            return 'document-' . uniqid();
        }

        $subject = '';

        if ($detail->document_type === 'Berita Acara' && $detail->beritaAcara) {
            $subject = $detail->beritaAcara->nama_pelanggan;
        } elseif ($detail->document_type === 'Resignation Letter' && $detail->resignLetter) {
            $subject = $detail->resignLetter->employee_name;
        } else {
            $subject = $this->file_name ? pathinfo($this->file_name, PATHINFO_FILENAME) : 'document';
        }

        $slug = Str::slug($subject);

        // Cek duplikat
        $count = static::where('slug', 'like', "{$slug}%")->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }

    // ✅ Tambahkan relasi ini
    public function documentDetail()
    {
        return $this->hasOne(DocumentDetail::class, 'document_id', 'id');
    }
}
