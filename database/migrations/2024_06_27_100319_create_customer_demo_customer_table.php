<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerDemoCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_demo_customer', function (Blueprint $table) {
            $table->id();
            $table->integer('demo_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->tinyInteger('join')->nullable()->comment('Trạng thái tham gia - 0: không tham gia; 1: tham gia');
            $table->tinyInteger('sign')->nullable()->comment('Trạng thái đăng ký - 0: không đăng ký; 1: đăng ký');
            $table->text('comment')->nullable()->comment('speaker nhận xét');
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
        Schema::dropIfExists('customer_demo_customer');
    }
}
