<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

        Route::post('nhan-vien', 'UserController@index');
        Route::post('tao-nhan-vien', 'UserController@store');
        Route::post('active-nhan-vien/{id}', 'UserController@changeActive');
        Route::post('update-nhan-vien/{id}', 'UserController@update');

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

        Route::post('doi-tac', 'BusinessPartnerController@index');
        Route::post('chi-tiet-doi-tac/{id}', 'BusinessPartnerController@edit');
        Route::post('tao-doi-tac', 'BusinessPartnerController@store');
        Route::post('update-doi-tac/{id}', 'BusinessPartnerController@update');

        Route::post('thi-truong', 'BusinessMarketController@index');
        Route::post('chi-tiet-thi-truong/{id}', 'BusinessMarketController@edit');
        Route::post('tao-thi-truong', 'BusinessMarketController@store');
        Route::post('update-thi-truong/{id}', 'BusinessMarketController@update');

        Route::post('chi-tieu', 'BusinessSpendingController@index');
        Route::post('chi-tiet-chi-tieu/{id}', 'BusinessSpendingController@edit');
        Route::post('tao-chi-tieu', 'BusinessSpendingController@store');
        Route::post('update-chi-tieu/{id}', 'BusinessSpendingController@update');

        Route::post('chinh-sach', 'BusinessPolicyController@index');
        Route::post('chi-tiet-chinh-sach/{id}', 'BusinessPolicyController@edit');
        Route::post('tao-chinh-sach', 'BusinessPolicyController@store');
        Route::post('update-chinh-sach/{id}', 'BusinessPolicyController@update');

    });

});
