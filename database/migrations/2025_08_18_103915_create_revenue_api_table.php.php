<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('revenue_api', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('periode');
            $table->text('cust_order_number')->nullable();
            $table->text('product_label')->nullable();
            $table->text('customer_name')->nullable();
            $table->text('product_name')->nullable();
            $table->text('product_group_name')->nullable();
            $table->text('lccd')->nullable();
            $table->text('regional')->nullable();
            $table->text('witel')->nullable();
            $table->text('rev_type')->nullable();
            $table->decimal('revenue', 19, 2)->nullable();
        });

        // Index
        Schema::table('revenue_api', function (Blueprint $table) {
            $table->index('periode', 'idx_revenue_periode');
        });
    }

    public function down()
    {
        Schema::dropIfExists('revenue_api');
    }
};
