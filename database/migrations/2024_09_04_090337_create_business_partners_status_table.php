<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessPartnersStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_partners_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customers')->nullable()->default(0)->comment('Số lượng khách hàng');
            $table->unsignedBigInteger('partner_id')->nullable()->comment('ID đối tác');
            $table->unsignedBigInteger('amount_contract')->nullable()->default(0)->comment('Số tiền hợp đồng');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_partners_status');
    }
}
