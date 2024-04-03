<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessMarketFacebookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_market_facebook', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('link');
            $table->text('more_level'); // biến động theo phân khúc
            $table->integer('market_id');
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
        Schema::dropIfExists('business_market_facebook');
    }
}
