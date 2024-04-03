<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_markets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->tinyInteger('segment')->default(0)->comment('0: Tiểu học; 1: THCS; 2: THPT; 3: Sinh viên; 4: Người đi làm');
            $table->string('link_map');
            $table->integer('city_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('campuses_id')->nullable();
            $table->tinyInteger('potential')->default(0)->comment('0: Cao; 1: Trung bình; 2: Thấp'); // tiềm năng
            $table->string('note');
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
        Schema::dropIfExists('business_markets');
    }
}
