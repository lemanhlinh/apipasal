<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDayShiftLearnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Thời gian học
        Schema::create('day_shift_learn', function (Blueprint $table) {
            $table->id();
            $table->string('title');
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
        Schema::dropIfExists('day_shift_learn');
    }
}
