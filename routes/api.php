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
    Route::post('update-trung-tam', 'CampusesController@update');

    Route::post('phong-ban', 'DepartmentController@index');
    Route::post('tao-phong-ban', 'DepartmentController@store');
    Route::post('update-phong-ban', 'DepartmentController@update');

    Route::post('chuc-vu', 'RegenciesController@index');
    Route::post('tao-chuc-vu', 'RegenciesController@store');
    Route::post('update-chuc-vu', 'RegenciesController@update');

    Route::post('nhan-vien', 'UserController@index');
    Route::post('tao-nhan-vien', 'UserController@store');
    Route::post('update-nhan-vien', 'UserController@update');

    Route::post('nhom-khoa-hoc', 'CourseCategoriesController@index');
    Route::post('tao-nhom-khoa-hoc', 'CourseCategoriesController@store');
    Route::post('update-nhom-khoa-hoc', 'CourseCategoriesController@update');

    Route::post('khoa-hoc', 'CoursesController@index');
    Route::post('tao-khoa-hoc', 'CoursesController@store');
    Route::post('update-khoa-hoc', 'CoursesController@update');

    Route::post('nhom-san-pham', 'ProductCategoriesController@index');
    Route::post('tao-nhom-san-pham', 'ProductCategoriesController@store');
    Route::post('update-nhom-san-pham', 'ProductCategoriesController@update');

    Route::post('san-pham', 'ProductsController@index');
    Route::post('tao-san-pham', 'ProductsController@store');
    Route::post('update-san-pham', 'ProductsController@update');

    Route::post('thoi-gian-hoc', 'DayShiftLearnController@index');
    Route::post('tao-thoi-gian-hoc', 'DayShiftLearnController@store');
    Route::post('update-thoi-gian-hoc', 'DayShiftLearnController@update');

    Route::post('lich-hoc', 'CalendarLearnController@index');
    Route::post('tao-lich-hoc', 'CalendarLearnController@store');
    Route::post('update-lich-hoc', 'CalendarLearnController@update');

    Route::post('ca-hoc', 'TimeStudyController@index');
    Route::post('tao-ca-hoc', 'TimeStudyController@store');
    Route::post('update-ca-hoc', 'TimeStudyController@update');
});
