<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
*/

// 服务商控制器
Route::get('sms_gateway/list', 'SmsGatewayController@list');
Route::get('sms_gateway/ajaxList', 'SmsGatewayController@ajaxList');
Route::post('sms_gateway/saveDefault', 'SmsGatewayController@saveDefault');
Route::any('sms_gateway/edit', 'SmsGatewayController@edit');

// 模板控制器
Route::get('sms_temp/list', 'SmsTempController@list');
Route::get('sms_temp/ajaxList', 'SmsTempController@ajaxList');
Route::any('sms_temp/edit', 'SmsTempController@edit');
Route::post('sms_temp/del', 'SmsTempController@del');

// 短信记录
Route::get('sms_log/list', 'SmsLogController@list');
Route::get('sms_log/ajaxList', 'SmsLogController@ajaxList');
Route::post('sms_log/del', 'SmsLogController@del');
