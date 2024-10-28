<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerClassOpening extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_class_opening', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->date('date_opening_old');
            $table->date('date_opening_new');
            $table->text('note')->nullable();
            $table->integer('days')->default(0)->comment('Số ngày còn lùi');
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
        Schema::dropIfExists('customer_class_opening');
    }
}
