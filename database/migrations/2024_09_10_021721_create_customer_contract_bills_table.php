<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerContractBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_contract_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->tinyInteger('bill_type')->comment('1: Thu; 2: Chi');
            $table->tinyInteger('transaction_type')->comment('1: Học phí; 2: Bảo lưu; 3: Học lại');
            $table->unsignedBigInteger('amount');
            $table->string('bill_number')->comment('Mã số phiếu thu');
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
        Schema::dropIfExists('customer_contract_bills');
    }
}
