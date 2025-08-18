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
        Schema::create('details_resign_letter', function (Blueprint $table) {
            $table->id(); // primary key: detail_id
            $table->string('employee_name');
            $table->string('employee_id');
            $table->date('last_day_of_work');
            $table->text('reason')->nullable();
            $table->foreignId('document_detail_id')->constrained()->onDelete('cascade');
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('details_resign_letter');
    }
};
