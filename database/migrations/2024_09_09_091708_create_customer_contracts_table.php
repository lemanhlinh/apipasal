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
            // $table->integer('offer_extra')->default(0);
            $table->unsignedBigInteger('manage_id');
            $table->unsignedBigInteger('user_id');
          
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('amount_offer');
            $table->unsignedBigInteger('amount_special');
            $table->unsignedBigInteger('amount_promotion');

            $table->unsignedBigInteger('campuse_id')->default(0);
            $table->unsignedBigInteger('type_study')->default(1)->comment('Hình thức học - 1: Online; 2: Offline');
            $table->tinyInteger('month_id')->default(0)->comment('Tháng muốn học');
            $table->unsignedBigInteger('time_study_id')->default(0)->comment('Ca muốn học');
            $table->unsignedBigInteger('day_shift_learn_id')->default(0)->comment('Thời gian muốn học');
            $table->unsignedBigInteger('calendar_learn_id')->default(0)->comment('Lịch muốn học');

            $table->tinyInteger('active')->default(1)->comment('1: Active; 0: Hủy');

            // $table->string('bill_number')->nullable()->comment('Mã số phiếu thu');
            // $table->date('date_payment')->comment('Ngày đóng tiền');
            // $table->text('note')->comment('Ghi chú');



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
