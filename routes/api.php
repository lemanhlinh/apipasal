<?php

use App\Http\Controllers\Api\BusinessMarketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Api\RegenciesController;


use App\Http\Controllers\Api\BusinessSpendingController;
use App\Http\Controllers\Api\BusinessPolicyController;
use App\Http\Controllers\Api\BusinessPartnerController;

use App\Http\Controllers\Api\Customer\CustomerController;
use App\Http\Controllers\Api\Customer\ChangeManagerController;
use App\Http\Controllers\Api\Customer\DemoController;
use App\Http\Controllers\Api\Customer\StudentController;
use App\Http\Controllers\Api\Customer\ContractController;
use App\Http\Controllers\Api\Customer\BillController;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PermissionController;

use App\Http\Controllers\Api\CourseCategoriesController;
use App\Http\Controllers\Api\CoursesController;

use App\Http\Controllers\Api\ProductCategoriesController;
use App\Http\Controllers\Api\ProductsController;

use App\Http\Controllers\Api\DayShiftLearnController;
use App\Http\Controllers\Api\CalendarLearnController;
use App\Http\Controllers\Api\Customer\DebtController;
use App\Http\Controllers\Api\TimeStudyController;

use App\Http\Controllers\Api\DepartmentController;

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
        // Campuses
        Route::post('trung-tam', 'CampusesController@index')->middleware('permission:view_campuses');
        Route::post('check-tao-trung-tam', 'CampusesController@create')->middleware('permission:create_campuses');
        Route::post('tao-trung-tam', 'CampusesController@store')->middleware('permission:create_campuses');
        Route::group(['middleware' => ['permission:edit_campuses']], function () {
            Route::post('update-trung-tam/{id}', 'CampusesController@update');
            Route::post('active-trung-tam/{id}', 'CampusesController@changeActive');
        });
        Route::post('delete-trung-tam/{id}', 'CampusesController@delete')->middleware('permission:delete_campuses');

        // Departments
        Route::post('phong-ban', [DepartmentController::class, 'index'])->middleware('permission:view_departments');
        Route::post('phong-ban-all', [DepartmentController::class, 'listAll'])->middleware('permission:view_departments');
        Route::post('phong-ban-phu/{id}', [DepartmentController::class, 'listSub'])->middleware('permission:view_departments');
        Route::post('tao-phong-ban', [DepartmentController::class, 'store'])->middleware('permission:create_departments');
        Route::group(['middleware' => ['permission:edit_departments']], function () {
            Route::post('active-phong-ban/{id}', [DepartmentController::class, 'changeActive']);
            Route::post('update-phong-ban/{id}', [DepartmentController::class, 'update']);
        });
        Route::post('delete-phong-ban/{id}', [DepartmentController::class, 'delete'])->middleware('permission:delete_departments');

        // Regencies
        Route::post('chuc-vu', [RegenciesController::class, 'index'])->middleware('permission:view_regencies');
        Route::post('tao-chuc-vu', [RegenciesController::class, 'store'])->middleware('permission:create_regencies');
        Route::group(['middleware' => ['permission:edit_regencies']], function () {
            Route::post('active-chuc-vu/{id}', [RegenciesController::class, 'changeActive']);
            Route::post('update-chuc-vu/{id}', [RegenciesController::class, 'update']);
        });
        Route::post('delete-chuc-vu/{id}', [RegenciesController::class, 'delete'])->middleware('permission:delete_regencies');

        // Permissions
        Route::post('all-permission', [PermissionController::class, 'index'])->middleware('permission:view_permissions');
        Route::post('role-permission', [PermissionController::class, 'rolePermission'])->middleware('permission:view_permissions');
        Route::post('save-permission', [PermissionController::class, 'savePermission'])->middleware('permission:view_permissions');
        Route::post('delete-permission', [PermissionController::class, 'deletePermission'])->middleware('permission:delete_permissions');

        // Users
        Route::post('nhan-vien', [UserController::class, 'index'])->middleware('permission:view_user');
        Route::post('tao-nhan-vien', [UserController::class, 'store'])->middleware('permission:create_user');
        Route::group(['middleware' => ['permission:edit_user']], function () {
            Route::post('active-nhan-vien/{id}', [UserController::class, 'changeActive']);
            Route::post('update-nhan-vien/{id}', [UserController::class, 'update']);
        });
        Route::post('xoa-nhan-vien/{id}', [UserController::class, 'delete'])->middleware('permission:delete_user');

        // Course Categories
        Route::post('nhom-khoa-hoc', [CourseCategoriesController::class, 'index'])->middleware('permission:view_course_categories');
        Route::post('tao-nhom-khoa-hoc', [CourseCategoriesController::class, 'store'])->middleware('permission:create_course_categories');
        Route::group(['middleware' => ['permission:edit_course_categories']], function () {
            Route::post('active-nhom-khoa-hoc/{id}', [CourseCategoriesController::class, 'changeActive']);
            Route::post('update-nhom-khoa-hoc/{id}', [CourseCategoriesController::class, 'update']);
        });
        Route::post('delete-nhom-khoa-hoc/{id}', [CourseCategoriesController::class, 'delete'])->middleware('permission:delete_course_categories');

        // Courses
        Route::post('khoa-hoc', [CoursesController::class, 'index'])->middleware('permission:view_courses');
        Route::post('tao-khoa-hoc', [CoursesController::class, 'store'])->middleware('permission:create_courses');
        Route::group(['middleware' => ['permission:edit_courses']], function () {
            Route::post('active-khoa-hoc/{id}', [CoursesController::class, 'changeActive']);
            Route::post('update-khoa-hoc/{id}', [CoursesController::class, 'update']);
        });
        Route::post('delete-khoa-hoc/{id}', [CoursesController::class, 'delete'])->middleware('permission:delete_courses');

        // Product Categories
        Route::post('nhom-san-pham', [ProductCategoriesController::class, 'index'])->middleware('permission:view_product_categories');
        Route::post('tao-nhom-san-pham', [ProductCategoriesController::class, 'store'])->middleware('permission:create_product_categories');
        Route::group(['middleware' => ['permission:edit_product_categories']], function () {
            Route::post('update-nhom-san-pham/{id}', [ProductCategoriesController::class, 'update']);
            Route::post('active-nhom-san-pham/{id}', [ProductCategoriesController::class, 'changeActive']);
        });
        Route::post('delete-nhom-san-pham/{id}', [ProductCategoriesController::class, 'delete'])->middleware('permission:delete_product_categories');

        // Products
        Route::post('san-pham', [ProductsController::class, 'index'])->middleware('permission:view_products');
        Route::post('tao-san-pham', [ProductsController::class, 'store'])->middleware('permission:create_products');
        Route::group(['middleware' => ['permission:edit_products']], function () {
            Route::post('active-san-pham/{id}', [ProductsController::class, 'changeActive']);
            Route::post('update-san-pham/{id}', [ProductsController::class, 'update']);
        });
        Route::post('delete-san-pham/{id}', [ProductsController::class, 'delete'])->middleware('permission:delete_products');

        //thoi gian hoc
        Route::post('thoi-gian-hoc', [DayShiftLearnController::class, 'index'])->middleware('permission:view_day_shift_learn');
        Route::post('tao-thoi-gian-hoc', [DayShiftLearnController::class, 'store'])->middleware('permission:create_day_shift_learn');
        Route::group(['middleware' => ['permission:edit_calendar_learn']], function () {
            Route::post('update-thoi-gian-hoc/{id}', [DayShiftLearnController::class, 'update']);
            Route::post('active-thoi-gian-hoc/{id}', [DayShiftLearnController::class, 'changeActive']);
        });
        Route::post('xoa-thoi-gian-hoc/{id}', [DayShiftLearnController::class, 'destroy'])->middleware('permission:delete_day_shift_learn');

        //lich hoc
        Route::post('lich-hoc', [CalendarLearnController::class, 'index'])->middleware('permission:view_calendar_learn');
        Route::group(['middleware' => ['permission:edit_calendar_learn']], function () {
            Route::post('update-lich-hoc/{id}', [CalendarLearnController::class, 'update']);
            Route::post('active-lich-hoc/{id}', [CalendarLearnController::class, 'changeActive']);
        });
        Route::post('tao-lich-hoc', [CalendarLearnController::class, 'store'])->middleware('permission:create_calendar_learn');
        Route::post('xoa-lich-hoc/{id}', [CalendarLearnController::class, 'destroy'])->middleware('permission:delete_calendar_learn');

        //Ca hoc
        Route::post('ca-hoc', [TimeStudyController::class, 'index'])->middleware('permission:view_time_study');
        Route::post('tao-ca-hoc', [TimeStudyController::class, 'store'])->middleware('permission:create_time_study');
        Route::group(['middleware' => ['permission:edit_time_study']], function () {
            Route::post('update-ca-hoc/{id}', [TimeStudyController::class, 'update']);
            Route::post('active-ca-hoc/{id}', [TimeStudyController::class, 'changeActive']);
        });
        Route::post('xoa-ca-hoc/{id}', [TimeStudyController::class, 'destroy'])->middleware('permission:delete_time_study');

        //Nguon khach hang
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

        Route::post('doi-tac', [BusinessPartnerController::class, 'index'])->middleware('permission:view_partners');
        Route::post('chi-tiet-doi-tac/{id}', [BusinessPartnerController::class, 'edit'])->middleware('permission:edit_partners');
        Route::post('tao-doi-tac', [BusinessPartnerController::class, 'store'])->middleware('permission:create_partners');
        Route::post('update-doi-tac/{id}', [BusinessPartnerController::class, 'update']);
        Route::post('active-doi-tac/{id}', [BusinessPartnerController::class, 'changeActive']);
        Route::post('delete-doi-tac/{id}', [BusinessPartnerController::class, 'delete']);

        Route::post('thi-truong', [BusinessMarketController::class, 'index']);
        Route::post('thi-truong-theo-hoc-vien', [BusinessMarketController::class, 'topHocVienThiTruong']);
        Route::post('thi-truong/update-thong-ke', [BusinessMarketController::class, 'saveStatistical']);
        Route::post('thi-truong/thong-ke', [BusinessMarketController::class, 'statistical']);
        Route::get('thi-truong/chi-tiet/{id}', [BusinessMarketController::class, 'detail']);
        Route::post('thi-truong/edit/{id}', [BusinessMarketController::class, 'edit']);
        Route::post('tao-thi-truong', [BusinessMarketController::class, 'store']);
        Route::post('group-facebook', [BusinessMarketController::class, 'group_facebook']);
        Route::post('history-market', [BusinessMarketController::class, 'history_market']);
        Route::post('thi-truong/cap-nhat/{id}', [BusinessMarketController::class, 'update']);

        Route::post('chi-tieu', [BusinessSpendingController::class, 'index']);
        Route::post('chi-tiet-chi-tieu/{id}', [BusinessSpendingController::class, 'edit']);
        Route::post('tao-chi-tieu', [BusinessSpendingController::class, 'store']);
        Route::post('update-chi-tieu/{id}', [BusinessSpendingController::class, 'update']);
        Route::post('active-chi-tieu/{id}', [BusinessSpendingController::class, 'changeActive']);
        Route::post('delete-chi-tieu/{id}', [BusinessSpendingController::class, 'delete']);

        Route::post('chinh-sach', [BusinessPolicyController::class, 'index']);
        Route::post('chi-tiet-chinh-sach/{id}', [BusinessPolicyController::class, 'edit']);
        Route::post('tao-chinh-sach', [BusinessPolicyController::class, 'store']);
        Route::post('update-chinh-sach/{id}', [BusinessPolicyController::class, 'update']);
        Route::post('delete-chinh-sach/{id}', [BusinessPolicyController::class, 'delete']);
        Route::post('active-chinh-sach/{id}', [BusinessPolicyController::class, 'changeActive']);

        # Customer\Customer
        Route::post('khach-hang/them-khach-hang', [CustomerController::class, 'store']);
        Route::post('khach-hang/danh-sach-khach-hang', [CustomerController::class, 'index']);
        Route::post('khach-hang/cap-nhat-khach-hang', [CustomerController::class, 'update']);
        Route::post('khach-hang/chi-tiet-khach-hang', [CustomerController::class, 'detail']);
        Route::post('khach-hang/thong-ke-khach-hang', [CustomerController::class, 'statistics']);
        Route::post('khach-hang/lich-su-cap-nhat', [CustomerController::class, 'historyUpdate']);

        # Customer\ChangeManager
        Route::post('khach-hang/doi-quan-ly/danh-sach', [ChangeManagerController::class, 'index']);
        Route::post('khach-hang/doi-quan-ly/them', [ChangeManagerController::class, 'store']);
        Route::post('khach-hang/doi-quan-ly/cap-nhat', [ChangeManagerController::class, 'update']);

        # Customer\Demo
        Route::post('khach-hang/danh-sach-demo-trai-nghiem', [DemoController::class, 'index']);
        Route::post('khach-hang/them-demo-trai-nghiem', [DemoController::class, 'store']);
        Route::post('khach-hang/cap-nhat-demo-trai-nghiem', [DemoController::class, 'update']);

        # Customer\Student
        Route::post('khach-hang/hoc-vien/danh-sach', [StudentController::class, 'index']);
        Route::post('khach-hang/hoc-vien/chi-tiet', [StudentController::class, 'detail']);
        Route::post('khach-hang/hoc-vien/them', [StudentController::class, 'store']);
        Route::post('khach-hang/hoc-vien/cap-nhat', [StudentController::class, 'update']);
        Route::post('khach-hang/hoc-vien/thong-ke', [StudentController::class, 'statistics']);

        # Customer\Contract
        Route::post('khach-hang/hop-dong/danh-sach', [ContractController::class, 'index']);
        Route::post('khach-hang/hop-dong/them', [ContractController::class, 'store']);
        Route::post('khach-hang/hop-dong/cap-nhat', [ContractController::class, 'update']);
        Route::post('khach-hang/hop-dong/thong-ke', [ContractController::class, 'statistics']);

        # Customer\Bill
        Route::post('khach-hang/hoa-don/danh-sach', [BillController::class, 'index']);
        Route::post('khach-hang/hoa-don/them', [BillController::class, 'store']);
        Route::post('khach-hang/hoa-don/cap-nhat', [BillController::class, 'update']);
        Route::post('khach-hang/hoa-don/xoa', [BillController::class, 'remove']);

        # Customer\Debt
        Route::post('khach-hang/cong-no/them', [DebtController::class, 'store']);
        Route::post('khach-hang/cong-no/cap-nhat', [DebtController::class, 'update']);
        Route::post('khach-hang/cong-no/xoa', [DebtController::class, 'remove']);
        Route::post('khach-hang/cong-no/thong-ke-nhanh', [DebtController::class, 'stats']);
        Route::post('khach-hang/cong-no/du-bao', [DebtController::class, 'forecast']);

        Route::post('danh-sach-quoc-gia', 'CountriesController@index');
        Route::post('danh-sach-tinh-thanh', 'CitiesController@index');
        Route::post('danh-sach-quan-huyen', 'DistrictsController@index');
    });
});
