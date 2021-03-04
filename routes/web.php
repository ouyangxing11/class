<?php
use Illuminate\Support\Facades\Redis;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::any("index/index",'IndexController@index');
Route::any("index/redis",'IndexController@redis');
Route::any("index/dbtest",'IndexController@dbtest');
Route::any("index/queue",'IndexController@queue');
Route::any("index/test",'IndexController@test');
Route::any("index/seckill",'IndexController@seckill');
Route::any("index/fbnc/{n}",'IndexController@fib_recursive');
Route::any("index/redis_pip",'IndexController@redis_pipline');
Route::any("index/getDistance",'IndexController@getDistance');
Route::any("index/test_yield",'IndexController@test_yield');
Route::any("index/merge",'IndexController@array_merge');
Route::any("test/test",'TestController@test');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('publish', function () {
    // Route logic...
    Redis::publish('test-channel', json_encode(['foo' => 'dsfsdfsdfs']));
});

//$router->group(['namespace' => 'Common','prefix' => 'common'], function() use ($router) {
//    $router->any("socket","FsoketOpenController@socket");
//    $router->any("test","FsoketOpenController@test");
//});
