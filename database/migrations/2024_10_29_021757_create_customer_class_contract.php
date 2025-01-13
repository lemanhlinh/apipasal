<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerClassContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_class_contract', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('class_id')->default(0);
            // $table->unsignedBigInteger('campuse_id')->default(0);
            // $table->unsignedBigInteger('type')->default(1)->comment('Hình thức học - 1: Online; 2: Offline');
            // $table->tinyInteger('month_id')->default(0)->comment('Tháng muốn học');
            // $table->unsignedBigInteger('time_study_id')->default(0)->comment('Ca muốn học');
            // $table->unsignedBigInteger('day_shift_learn_id')->default(0)->comment('Thời gian muốn học');
            // $table->unsignedBigInteger('calendar_learn_id')->default(0)->comment('Lịch muốn học');
            $table->unsignedBigInteger('status_admission')->default(1)->comment('Trạng thái tuyển sinh - 1: Chưa chọn lớp; 2: Chờ lớp; 3: Đã có lớp');
            $table->tinyInteger('status_study')->default(1)->comment('Trạng thái học tập - 1: Chưa học; 2: Đang học; 3: Bảo lưu; 4: Huỷ; 5: Học lại; 6: Học lên; 7: Học lên CK; 8: Học lại SBL; 9: Đã học xong; 10: Chuyển lớp');

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
        Schema::dropIfExists('customer_class_contract');
    }
}
