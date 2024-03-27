<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'api', 'namespace' => 'Api'], function () {
    Route::post('trung-tam', 'CampusesController@index');
    Route::post('tao-trung-tam', 'CampusesController@store');
    Route::post('update-trung-tam/{id}', 'CampusesController@update');

    Route::post('phong-ban', 'DepartmentController@index');
    Route::post('phong-ban-all', 'DepartmentController@listAll');
    Route::post('phong-ban-phu/{id}', 'DepartmentController@listSub');
    Route::post('tao-phong-ban', 'DepartmentController@store');
    Route::post('update-phong-ban/{id}', 'DepartmentController@update');

    Route::post('chuc-vu', 'RegenciesController@index');
    Route::post('tao-chuc-vu', 'RegenciesController@store');
    Route::post('update-chuc-vu/{id}', 'RegenciesController@update');

    Route::post('nhan-vien', 'UserController@index');
    Route::post('tao-nhan-vien', 'UserController@store');
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
    Route::post('update-san-pham/{id}', 'ProductsController@update');

    Route::post('thoi-gian-hoc', 'DayShiftLearnController@index');
    Route::post('tao-thoi-gian-hoc', 'DayShiftLearnController@store');
    Route::post('update-thoi-gian-hoc/{id}', 'DayShiftLearnController@update');

    Route::post('lich-hoc', 'CalendarLearnController@index');
    Route::post('tao-lich-hoc', 'CalendarLearnController@store');
    Route::post('update-lich-hoc/{id}', 'CalendarLearnController@update');

    Route::post('ca-hoc', 'TimeStudyController@index');
    Route::post('tao-ca-hoc', 'TimeStudyController@store');
    Route::post('update-ca-hoc/{id}', 'TimeStudyController@update');

    Route::post('nguon-khach-hang', 'BusinessSettingSourceCustomerController@index');
    Route::post('tao-nguon-khach-hang', 'BusinessSettingSourceCustomerController@store');
    Route::post('update-nguon-khach-hang/{id}', 'BusinessSettingSourceCustomerController@update');

    Route::post('loai-demo-trai-nghiem', 'BusinessSettingDemoExperienceController@index');
    Route::post('tao-loai-demo-trai-nghiem', 'BusinessSettingDemoExperienceController@store');
    Route::post('update-loai-demo-trai-nghiem/{id}', 'BusinessSettingDemoExperienceController@update');

    Route::post('ngay-vao-kho', 'BusinessSettingWareHouseRuleController@index');
    Route::post('tao-ngay-vao-kho', 'BusinessSettingWareHouseRuleController@store');
    Route::post('update-ngay-vao-kho/{id}', 'BusinessSettingWareHouseRuleController@update');

    Route::post('doi-tac', 'BusinessPartnerController@index');
    Route::post('tao-doi-tac', 'BusinessPartnerController@store');
    Route::post('update-doi-tac/{id}', 'BusinessPartnerController@update');
});
