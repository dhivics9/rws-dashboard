<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('details_berita_acara', function (Blueprint $table) {
            $table->id(); // primary key: detail_id
            $table->string('nama_pelanggan');
            $table->string('lokasi_kerja');
            $table->string('jenis_layanan');
            $table->string('mo');
            $table->string('sid');
            $table->string('bw_prev');
            $table->string('bw_new');
            $table->date('tanggal_mulai');
            $table->foreignId('document_detail_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('details_berita_acara');
    }
};
