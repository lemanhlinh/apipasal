<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerCustomerStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_customer_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->date('date');

            $table->unsignedBigInteger('primary_school')->nullable()->default(0);
            $table->unsignedBigInteger('secondary_school')->nullable()->default(0);
            $table->unsignedBigInteger('high_school')->nullable()->default(0);
            $table->unsignedBigInteger('college')->nullable()->default(0);
            $table->unsignedBigInteger('working')->nullable()->default(0);

            $table->unsignedBigInteger('customer_success')->nullable()->default(0);
            $table->unsignedBigInteger('customer_new')->nullable()->default(0);
            $table->unsignedBigInteger('customer_depot')->nullable()->default(0);
            $table->unsignedBigInteger('customer_total')->nullable()->default(0);

            $table->unsignedBigInteger('contract_total')->nullable()->default(0);
            $table->unsignedBigInteger('contract_success')->nullable()->default(0);
            $table->unsignedBigInteger('contract_expired')->nullable()->default(0);


            $table->timestamps();

            $table->unique(['customer_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_customer_status');
    }
}
