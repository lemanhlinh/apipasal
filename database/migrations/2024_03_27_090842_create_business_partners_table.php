<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_partners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('phone');
            $table->string('email');
            $table->tinyInteger('type')->default(0)->comment('0: Truyền thông; 1: Liên kết tuyển sinh');
            $table->tinyInteger('segment')->default(0)->comment('0: Tiểu học; 1: THCS; 2: THPT; 3: Sinh viên; 4: Người đi làm');
            $table->integer('campuses_id');
            $table->string('info_partner');
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
        Schema::dropIfExists('business_partners');
    }
}
