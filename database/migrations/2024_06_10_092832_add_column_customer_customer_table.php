<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCustomerCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_customer', function (Blueprint $table) {
            $table->tinyInteger('contract')->default(0)->comment('Cơ hội hợp đồng')->after('product');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_customer', function (Blueprint $table) {
            $table->tinyInteger('contract');
        });
    }
}
