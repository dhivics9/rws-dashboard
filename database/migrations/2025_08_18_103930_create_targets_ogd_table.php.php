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
            $table->text('regional')->nullable();
            $table->text('witel')->nullable();
            $table->text('lccd')->nullable();
            $table->text('stream')->nullable();
            $table->text('product_name')->nullable();
            $table->text('gl_account')->nullable();
            $table->text('bp_number')->nullable();
            $table->text('customer_name')->nullable();
            $table->text('customer_type')->nullable();
            $table->decimal('target', 19, 2)->nullable();
            $table->decimal('revenue', 19, 2)->nullable();
            $table->integer('periode');
            $table->decimal('target_rkapp', 19, 2)->nullable();
        });

        // Indexes
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
