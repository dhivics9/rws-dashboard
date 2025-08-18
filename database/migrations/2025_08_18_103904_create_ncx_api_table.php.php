<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ncx_api', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->text('li_product_name')->nullable();
            $table->text('ca_account_name')->nullable();
            $table->text('order_id')->nullable();
            $table->text('li_sid')->nullable();
            $table->text('quote_subtype')->nullable();
            $table->text('sa_x_addr_city')->nullable();
            $table->double('sa_x_addr_latitude', 18, 15)->nullable();
            $table->double('sa_x_addr_latitude2', 18, 15)->nullable();
            $table->double('sa_x_addr_longlitude', 18, 15)->nullable(); // typo: longitude
            $table->double('sa_x_addr_longlitude2', 18, 15)->nullable();
            $table->text('billing_type_cd')->nullable();
            $table->text('price_type_cd')->nullable();
            $table->decimal('x_mrc_tot_net_pri', 19, 2)->nullable();
            $table->decimal('x_nrc_tot_net_pri', 19, 2)->nullable();
            $table->text('quote_createdby_name')->nullable();
            $table->text('agree_num')->nullable();
            $table->text('agree_type')->nullable();
            $table->timestampTz('agree_end_date')->nullable();
            $table->text('agree_status')->nullable();
            $table->text('li_milestone')->nullable();
            $table->timestampTz('order_created_date')->nullable();
            $table->text('sa_witel')->nullable();
            $table->text('sa_account_status')->nullable();
            $table->text('sa_account_address_name')->nullable();
            $table->timestampTz('billing_activation_date')->nullable();
            $table->text('billing_activation_status')->nullable();
            $table->timestampTz('billcomp_date')->nullable();
            $table->timestampTz('li_milestone_date')->nullable();
            $table->text('witel')->nullable();
            $table->text('bw')->nullable();
        });

        // Index
        Schema::table('ncx_api', function (Blueprint $table) {
            $table->index('witel', 'idx_ncx_witel');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ncx_api');
    }
};
