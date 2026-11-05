<?php

use Illuminate\Support\Facades\Route;
use Jyim\LaravelPluginMarket\Http\Controllers\PluginsController;
use Jyim\LaravelPluginMarket\Http\Controllers\RegisterController;
use Jyim\LaravelPluginMarket\Http\Controllers\LoginController;
use Jyim\LaravelPluginMarket\Http\Controllers\UserController;
use Jyim\LaravelPluginMarket\Http\Middleware\AdminAuthorize;
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

Route::prefix("pluginmarket")->group(function (){
    Route::middleware("auth:sanctum")->group(function (){
        Route::resource('pluginversions',  'PluginsVersionsController');
        Route::post("plugins", [PluginsController::class,"store"]);
        Route::get("user-info", [UserController::class, "getUserInfo"]);
        Route::get("user/plugins", [UserController::class,"getPlugins"]);
        Route::post("plugins/download/{versionId}", [PluginsController::class,"download"])->middleware('throttle:10,1');
        // 上传收敛到全站唯一的 App\Http\Controllers\Common\UploadController：
        // 原来这里自己 Storage::put('image')，既没有扩展名白名单也没有魔数校验，
        // 而且落盘位置和 NAS 体系脱节（本地盘 storage/app），换环境就找不到文件
        Route::post('upload/image',  [\App\Http\Controllers\Common\UploadController::class, 'upload']);

        Route::middleware(AdminAuthorize::class)->group(function (){
            Route::resource('users', 'UserController');
            Route::resource('download-histories', 'PluginDownloadsController')->only('index');
        });
    });
    Route::get("plugins/count", [PluginsController::class,"count"]);
    Route::get("plugins", [PluginsController::class,"index"]);
    Route::post("register",[RegisterController::class, 'register']);
    Route::post("login",[LoginController::class, 'login']);
});