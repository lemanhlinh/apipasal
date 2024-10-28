<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedBigInteger('campuse_id')->default(0);
            $table->unsignedBigInteger('class_id')->default(0);
            $table->string('class_url')->nullable();
            $table->unsignedBigInteger('course_category_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('day_shift_learn_id')->comment('Thời gian học');
            $table->unsignedBigInteger('calendar_learn_id')->comment('Lịch học');
            $table->unsignedBigInteger('time_study_id')->comment('Ca học');
            $table->date('date_start');
            $table->date('date_end');
            $table->unsignedBigInteger('user_admission_id')->comment('Nhân viên tuyển sinh');
            $table->text('note')->nullable();

            $table->tinyInteger('status')->default(1)->comment('1: Sắp khai giảng, 2: Lùi khai giảng; 3: Đang học, 4: Đã kết thúc, 5: Đã hủy');

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
        Schema::dropIfExists('customer_classes');
    }
}
