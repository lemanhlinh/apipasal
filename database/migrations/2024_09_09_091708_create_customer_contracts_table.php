<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');

            $table->date('date_contract');
            $table->unsignedBigInteger('type')->comment('1: Hợp đồng mới; 2: Hợp đồng cross sale; 3: Hợp dồng tái tục');
            $table->unsignedBigInteger('product_category_id');
            $table->unsignedBigInteger('product_id');

            $table->unsignedBigInteger('special_id')->default(0);
            $table->unsignedBigInteger('promotion_id')->default(0);
            $table->integer('offer_extra')->default(0);
            $table->unsignedBigInteger('manage_id');

            $table->unsignedBigInteger('amount');
            $table->string('bill_number')->nullable()->comment('Mã số phiếu thu');
            $table->date('date_payment')->comment('Ngày đóng tiền');
            $table->text('note')->comment('Ghi chú');



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
        Schema::dropIfExists('customer_contracts');
    }
}
