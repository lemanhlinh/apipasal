<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerDemoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_demo', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->tinyInteger('campuses_type')->nullable()->comment('Loại trung tâm - 1: PKD; 2: PĐT; 3: Trung tâm');
            $table->integer('campuses_id')->nullable()->comment('Trung tâm');
            $table->integer('demo_id')->nullable()->comment('Loại demo - fs_business_setting_demo_experience');
            $table->tinyInteger('type')->nullable()->comment('Hình thức: 1: online; 2: offline');
            $table->string('address')->nullable()->comment('Địa điểm học');
            $table->date('date_start')->nullable()->comment('Ngày diễn ra');
            $table->date('date_end')->nullable()->comment('Ngày diễn kết thúc');
            $table->tinyInteger('schedule')->nullable()->comment('Lịch học: 1: Sáng; 2: Chiều; 3: Tối');
            $table->string('study')->nullable()->comment('Ca học');

            // $table->string('speaker_name')->nullable()->comment('Tên giảng viên');
            // $table->string('speaker_telephone')->nullable()->comment('Sđt giảng viên');
            // $table->string('url')->nullable()->comment('Đường dẫn demo');

            $table->integer('speaker_id')->nullable()->comment('Giảng viên');
            $table->integer('user_manage_id')->nullable()->comment('User phụ trách');
            $table->integer('user_id')->nullable()->comment('User tạo demo');

            $table->tinyInteger('active')->nullable()->comment('Trạng thái: 1: active (sắp - đang - kết thúc); 0: Hủy');
            $table->integer('invite')->nullable()->comment('Số lượng mời');

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
        Schema::dropIfExists('customer_demo');
    }
}
