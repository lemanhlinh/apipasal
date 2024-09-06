<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_customer', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('phone')->unique();
            $table->string('title')->nullable();
            $table->string('email')->nullable();
            $table->tinyInteger('sex')->default(0)->comment('1: Nam; 2: Nữ; 3: Khác');
            $table->string('year_birth')->nullable()->comment('Năm sinh');

            $table->integer('country')->nullable()->comment('Quốc gia');
            $table->string('country_name')->nullable()->comment('Quốc gia');
            $table->integer('province')->nullable()->comment('Thành phố');
            $table->string('province_name')->nullable()->comment('Thành phố');
            $table->integer('district')->nullable()->comment('Quận huyện');
            $table->string('district_name')->nullable()->comment('Quận huyện');
            $table->string('address')->nullable()->comment('Địa chỉ');

            $table->tinyInteger('segment')->default(1)->comment('Phân khúc khách hàng; 1: Tiểu học; 2: THCS; 3: THPT; 4: Sinh viên; 5: Người đi làm');
            $table->json('segment_detail')->nullable()->comment('Chi tiết phân khúc khách hàng');

            $table->tinyInteger('source')->default(1)->comment('Nguồn khách hàng; 1: Nguồn mới khác; 2: Đối tác kênh bán; 3: Học viên giới thiệu');
            $table->integer('source_detail')->default(1)->comment('Id nguồn tương ứng');

            $table->text('issue')->nullable()->comment('Vấn đề - Nhu cầu');
            $table->json('consulting_detail')->nullable()->comment('Lịch sử chăm sóc');
            $table->tinyInteger('consulting')->default(1)->comment('Trạng thái tư vấn; 1: Tiếp cận; 2: Tư vấn: 3: Xử lý từ chối; 4: Chăm sóc sau: 5: Bỏ');
            $table->timestamp('consulting_date')->nullable()->comment('Ngày chăm sóc gần nhất');
            $table->tinyInteger('potential')->default(1)->comment('Độ tiềm năng; 1: Thấp; 2: Trung bình; 3: Cao');
            
            $table->timestamp('date_registration')->nullable()->comment('Cơ hội hợp đồng - Ngày đăng ký dự kiến');
            $table->integer('product_category')->nullable()->comment('Cơ hội hợp đồng - Nhóm sản phẩm dự kiến');
            $table->integer('product')->nullable()->comment('Cơ hội hợp đồng - Sản phẩm dự kiến');
            $table->tinyInteger('contract')->default(0)->comment('Cơ hội hợp đồng');
            $table->integer('manage_id')->comment('Người quản lý');
            
            $table->tinyInteger('active')->default(0)->comment('0: kho; 1: mới; 2: học viên');
            $table->date('active_date')->nullable()->comment('Ngày chuyển trạng thái');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_customer');
    }
}
