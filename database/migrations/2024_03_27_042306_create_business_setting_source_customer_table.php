<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessSettingSourceCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_setting_source_customer', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code');
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
        Schema::dropIfExists('business_setting_source_customer');
    }
}
