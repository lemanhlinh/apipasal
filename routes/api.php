<?php

use App\Http\Controllers\Api\BusinessMarketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\BusinessSpendingController;
use App\Http\Controllers\Api\BusinessPolicyController;
use App\Http\Controllers\Api\BusinessPartnerController;

use App\Http\Controllers\Api\CustomerCustomerController;

use App\Http\Controllers\Api\Customer\CustomerController;
use App\Http\Controllers\Api\Customer\DemoController;
use App\Http\Controllers\Api\Customer\StudentController;
use App\Http\Controllers\Api\Customer\ContractController;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PermissionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api', 'namespace' => 'Api'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('trung-tam', 'CampusesController@index')->middleware('permission:view_campuses');
        Route::group(['middleware' => ['permission:create_campuses']], function () {
            Route::post('check-tao-trung-tam', 'CampusesController@create');
            Route::post('tao-trung-tam', 'CampusesController@store');
        });
        Route::group(['middleware' => ['permission:edit_campuses']], function () {
            Route::post('update-trung-tam/{id}', 'CampusesController@update');
            Route::post('active-trung-tam/{id}', 'CampusesController@changeActive');
        });

        Route::post('phong-ban', 'DepartmentController@index');
        Route::post('phong-ban-all', 'DepartmentController@listAll');
        Route::post('phong-ban-phu/{id}', 'DepartmentController@listSub');
        Route::post('active-phong-ban/{id}', 'DepartmentController@changeActive');
        Route::post('tao-phong-ban', 'DepartmentController@store');
        Route::post('update-phong-ban/{id}', 'DepartmentController@update');

        Route::post('chuc-vu', 'RegenciesController@index');
        Route::post('tao-chuc-vu', 'RegenciesController@store');
        Route::post('active-chuc-vu/{id}', 'RegenciesController@changeActive');
        Route::post('update-chuc-vu/{id}', 'RegenciesController@update');

        Route::post('all-permission', [PermissionController::class,'index']);
        Route::post('role-permission', [PermissionController::class,'rolePermission']);

        Route::post('nhan-vien', [UserController::class,'index']);
        Route::post('tao-nhan-vien', [UserController::class,'store']);
        Route::post('active-nhan-vien/{id}', [UserController::class,'changeActive']);
        Route::post('update-nhan-vien/{id}', [UserController::class,'update']);

        Route::post('nhom-khoa-hoc', 'CourseCategoriesController@index');
        Route::post('tao-nhom-khoa-hoc', 'CourseCategoriesController@store');
        Route::post('update-nhom-khoa-hoc/{id}', 'CourseCategoriesController@update');

        Route::post('khoa-hoc', 'CoursesController@index');
        Route::post('tao-khoa-hoc', 'CoursesController@store');
        Route::post('update-khoa-hoc/{id}', 'CoursesController@update');

        Route::post('nhom-san-pham', 'ProductCategoriesController@index');
        Route::post('tao-nhom-san-pham', 'ProductCategoriesController@store');
        Route::post('update-nhom-san-pham/{id}', 'ProductCategoriesController@update');

        Route::post('san-pham', 'ProductsController@index');
        Route::post('tao-san-pham', 'ProductsController@store');
        Route::post('active-san-pham/{id}', 'ProductsController@changeActive');
        Route::post('update-san-pham/{id}', 'ProductsController@update');

        Route::post('thoi-gian-hoc', 'DayShiftLearnController@index');
        Route::post('xoa-thoi-gian-hoc/{id}', 'DayShiftLearnController@destroy');
        Route::post('tao-thoi-gian-hoc', 'DayShiftLearnController@store');
        Route::post('update-thoi-gian-hoc/{id}', 'DayShiftLearnController@update');

        Route::post('lich-hoc', 'CalendarLearnController@index');
        Route::post('xoa-lich-hoc/{id}', 'CalendarLearnController@destroy');
        Route::post('tao-lich-hoc', 'CalendarLearnController@store');
        Route::post('update-lich-hoc/{id}', 'CalendarLearnController@update');

        Route::post('ca-hoc', 'TimeStudyController@index');
        Route::post('xoa-ca-hoc/{id}', 'TimeStudyController@destroy');
        Route::post('tao-ca-hoc', 'TimeStudyController@store');
        Route::post('update-ca-hoc/{id}', 'TimeStudyController@update');

        Route::post('nguon-khach-hang', 'BusinessSettingSourceCustomerController@index');
        Route::post('tao-nguon-khach-hang', 'BusinessSettingSourceCustomerController@store');
        Route::post('active-nguon-khach-hang/{id}', 'BusinessSettingSourceCustomerController@changeActive');
        Route::post('update-nguon-khach-hang/{id}', 'BusinessSettingSourceCustomerController@update');

        Route::post('loai-demo-trai-nghiem', 'BusinessSettingDemoExperienceController@index');
        Route::post('tao-loai-demo-trai-nghiem', 'BusinessSettingDemoExperienceController@store');
        Route::post('active-loai-demo-trai-nghiem/{id}', 'BusinessSettingDemoExperienceController@changeActive');
        Route::post('update-loai-demo-trai-nghiem/{id}', 'BusinessSettingDemoExperienceController@update');

        Route::post('ngay-vao-kho', 'BusinessSettingWareHouseRuleController@index');
        Route::post('tao-ngay-vao-kho', 'BusinessSettingWareHouseRuleController@store');
        Route::post('active-ngay-vao-kho/{id}', 'BusinessSettingWareHouseRuleController@changeActive');
        Route::post('update-ngay-vao-kho/{id}', 'BusinessSettingWareHouseRuleController@update');

        Route::post('doi-tac', [BusinessPartnerController::class, 'index']);
        Route::post('chi-tiet-doi-tac/{id}', [BusinessPartnerController::class, 'edit']);
        Route::post('tao-doi-tac', [BusinessPartnerController::class, 'store']);
        Route::post('update-doi-tac/{id}', [BusinessPartnerController::class, 'update']);

        Route::post('thi-truong', [BusinessMarketController::class, 'index']);
        Route::post('thi-truong-theo-hoc-vien', [BusinessMarketController::class, 'topHocVienThiTruong']);
        Route::post('thi-truong/update-thong-ke', [BusinessMarketController::class, 'saveStatistical']);
        Route::post('thi-truong/thong-ke', [BusinessMarketController::class, 'statistical']);

        Route::post('thi-truong/chi-tiet/{id}', [BusinessMarketController::class, 'edit']);
        Route::post('tao-thi-truong', [BusinessMarketController::class, 'store']);
        Route::post('group-facebook', [BusinessMarketController::class, 'group_facebook']);
        Route::post('history-market', [BusinessMarketController::class, 'history_market']);
        Route::post('thi-truong/cap-nhat/{id}', [BusinessMarketController::class, 'update']);

        Route::post('chi-tieu', [BusinessSpendingController::class, 'index']);
        Route::post('chi-tiet-chi-tieu/{id}', [BusinessSpendingController::class, 'edit']);
        Route::post('tao-chi-tieu', [BusinessSpendingController::class, 'store']);
        Route::post('update-chi-tieu/{id}', [BusinessSpendingController::class, 'update']);

        Route::post('chinh-sach', [BusinessPolicyController::class, 'index']);
        Route::post('chi-tiet-chinh-sach/{id}', [BusinessPolicyController::class, 'edit']);
        Route::post('tao-chinh-sach', [BusinessPolicyController::class, 'store']);
        Route::post('update-chinh-sach/{id}', [BusinessPolicyController::class, 'update']);

        # Customer\Customer
        Route::post('khach-hang/them-khach-hang', [CustomerController::class, 'store']);
        Route::post('khach-hang/danh-sach-khach-hang', [CustomerController::class, 'index']);
        Route::post('khach-hang/cap-nhat-khach-hang', [CustomerController::class, 'update']);
        Route::post('khach-hang/chi-tiet-khach-hang', [CustomerController::class, 'detail']);
        Route::post('khach-hang/doi-quan-ly-khach-hang', [CustomerController::class, 'changeManagement']);
        Route::post('khach-hang/thong-ke-khach-hang', [CustomerController::class, 'statistics']);

        # Customer\Demo
        Route::post('khach-hang/danh-sach-demo-trai-nghiem', [DemoController::class, 'index']);
        Route::post('khach-hang/them-demo-trai-nghiem', [DemoController::class, 'store']);
        Route::post('khach-hang/cap-nhat-demo-trai-nghiem', [DemoController::class, 'update']);

        # Customer\Student
        Route::post('khach-hang/hoc-vien/danh-sach', [StudentController::class, 'index']);
        Route::post('khach-hang/hoc-vien/them', [StudentController::class, 'store']);
        Route::post('khach-hang/hoc-vien/cap-nhat', [StudentController::class, 'update']);
        Route::post('khach-hang/hoc-vien/thong-ke', [StudentController::class, 'statistics']);

        # Customer\Contract
        Route::post('khach-hang/hop-dong/danh-sach', [ContractController::class, 'index']);
        Route::post('khach-hang/hop-dong/them', [ContractController::class, 'store']);
        Route::post('khach-hang/hop-dong/cap-nhat', [ContractController::class, 'update']);
        Route::post('khach-hang/hop-dong/thong-ke', [ContractController::class, 'statistics']);



        Route::post('danh-sach-quoc-gia', 'CountriesController@index');
        Route::post('danh-sach-tinh-thanh', 'CitiesController@index');
        Route::post('danh-sach-quan-huyen', 'DistrictsController@index');
    });

});
