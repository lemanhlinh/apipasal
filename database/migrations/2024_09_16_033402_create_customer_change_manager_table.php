<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerChangeManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_change_manager', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('new_user_id');
            $table->unsignedBigInteger('old_user_id');
            $table->text('reason');
            $table->text('admin_reason')->nullable();
            $table->tinyInteger('status')->comment('0: Chờ duyệt, 1: Duyệt 2: Hủy');
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
        Schema::dropIfExists('customer_change_manager');
    }
}
