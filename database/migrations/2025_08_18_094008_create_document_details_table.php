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
        Schema::create('document_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->string('nomor_dokumen')->nullable();
            $table->string('tipe_dokumen'); // Berita Acara, BAK, BA, PKS, PO, dll
            $table->boolean('bak')->default(false);
            $table->boolean('ba')->default(false);
            $table->boolean('pks')->default(false);
            $table->boolean('po')->default(false);
            $table->text('description')->nullable();
            $table->string('nama_pelanggan')->nullable();
            $table->string('lokasi_kerja')->nullable();
            $table->string('jenis_layanan')->nullable(); // dropdown: IP Transit, Metro E, dll
            $table->string('tipe_order')->nullable(); // SO, DO, MO, RO
            $table->string('sid')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_details');
    }
};
