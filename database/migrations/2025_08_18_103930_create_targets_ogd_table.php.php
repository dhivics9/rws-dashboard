<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('targets_ogd', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('regional', 255)->nullable();     // changed from text to string
            $table->string('witel', 255)->nullable();        // changed
            $table->string('lccd', 255)->nullable();         // changed
            $table->string('stream', 255)->nullable();       // changed
            $table->string('product_name', 255)->nullable(); // changed
            $table->string('gl_account', 255)->nullable();   // changed
            $table->string('bp_number', 255)->nullable();    // changed
            $table->string('customer_name', 255)->nullable(); // changed
            $table->string('customer_type', 50)->nullable(); // changed, adjust length as needed
            $table->decimal('target', 19, 2)->nullable();
            $table->decimal('revenue', 19, 2)->nullable();
            $table->integer('periode');
            $table->decimal('target_rkapp', 19, 2)->nullable();
        });

        // Now safely create indexes
        Schema::table('targets_ogd', function (Blueprint $table) {
            $table->index('periode', 'idx_targets_periode');
            $table->index('witel', 'idx_targets_witel');
        });
    }

    public function down()
    {
        Schema::dropIfExists('targets_ogd');
    }
};
