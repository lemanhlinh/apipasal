<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessPolicyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_policy', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->tinyInteger('type')->default(0)->comment('0: Chính sách khuyến mại; 1: Đối tượng đặc biệt');
            $table->tinyInteger('type_promotion')->default(0)->comment('0: Tiền mặt; 1: Phần trăm');
            $table->string('promotion')->comment('Giá trị giảm');
            $table->date('date_start');
            $table->date('date_end');
            $table->tinyInteger('active')->default(0)->comment('0: Không hoạt động; 1: Hoạt động');
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
        Schema::dropIfExists('business_policy');
    }
}
