<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerStudentStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_student_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date');

            $table->unsignedBigInteger('primary_school')->nullable()->default(0);
            $table->unsignedBigInteger('secondary_school')->nullable()->default(0);
            $table->unsignedBigInteger('high_school')->nullable()->default(0);
            $table->unsignedBigInteger('college')->nullable()->default(0);
            $table->unsignedBigInteger('working')->nullable()->default(0);

            $table->unsignedBigInteger('contract')->nullable()->default(0);
            $table->double('days')->nullable()->default(0)->comment('Số ngày trở thành học viên từ khi nhập khách hàng');
      
            $table->unique(['user_id', 'date']);
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
        Schema::dropIfExists('customer_student_status');
    }
}
