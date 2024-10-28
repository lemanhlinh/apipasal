<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('customer_segment_id'); 

            $table->tinyInteger('status')->default(1)->comment('Trạng thái học viên - 1: Mới; 2: Hủy; 3: Chuyển nhượng; 4: Nhận nhượng');
            $table->tinyInteger('status_admission')->default(1)->comment('Trạng thái tuyển sinh - 1: Chưa chọn lớp; 2: Chờ lớp; 3: Đã có lớp');
            $table->tinyInteger('status_study')->default(1)->comment('Trạng thái học tập - 1: Chưa học; 2: Đang học; 3: Bảo lưu; 4: Huỷ; 5: Học lại; 6: Học lên; 7: Học lên CK; 8: Học lại SBL; 9: Đã học xong; 10: Chuyển lớp');
   
            $table->unique(['customer_id', 'customer_segment_id']);
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
        Schema::dropIfExists('customer_students');
    }
}
