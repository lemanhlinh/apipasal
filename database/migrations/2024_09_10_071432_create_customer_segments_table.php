<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_segments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            
            $table->string('name')->nullable();
            $table->tinyInteger('gender')->default(0)->comment('0: Không chọn;1: Nam; 2: Nữ; 3: Khác');
            $table->unsignedBigInteger('district_id')->default(0);
            $table->unsignedBigInteger('market_id')->default(0)->comment('Trường học - Thị trường');
            $table->string('class')->nullable()->comment('Lớp');

            $table->date('birthday')->nullable();

            $table->json('parent')->nullable();
            $table->string('telephone')->nullable();
            $table->string('telephone_extra')->nullable();

            $table->tinyInteger('college_year')->default(0)->comment('Năm học 1-10');
            $table->unsignedBigInteger('college_major')->default(0)->comment('Chuyên ngành');

            $table->string('company')->nullable();
            $table->string('position')->nullable()->comment('chức vụ');
            $table->string('work')->nullable()->comment('Lĩnh vực');

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
        Schema::dropIfExists('customer_segments');
    }
}
