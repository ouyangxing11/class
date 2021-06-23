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
//Auth::routes();

Route::any('home/home', 'HomeController@index')->name('home');
Route::get('home/mongo', 'HomeController@mongo')->name('mongo');
Route::get('home/composer_test', 'HomeController@composer_test')->name('composer_test');
Route::get('home/old_fib/{n}', 'HomeController@old_fib');
Route::get('home/fib/{n}', 'HomeController@fib');
Route::get('home/new_fib/{n}', 'HomeController@new_fib');
Route::get('home/db/{amount}', 'HomeController@dp');
Route::get('home/egg', 'HomeController@fallegg1');
Route::get('home/money', 'HomeController@choose_monkey_king');
Route::get('home/neicun', 'HomeController@collect');
Route::get('home/filter', 'HomeController@filter');
Route::get('queue/main', 'Queue@main');
Route::get('queue/test/{i}', 'NewQueue@backtrack');

Route::any('es/index', 'EsController@index');
Route::any('es/get', 'EsController@get');
Route::any('wxtest/send_coupon', 'WxTestController@wx_send');
Route::any('bloom/add', 'FilteRepeatedComments@add_test');
Route::any('bloom/exist', 'FilteRepeatedComments@exists_test');

Route::get('publish', function () {
    // Route logic...
    Redis::publish('test-channel', json_encode(['foo' => 'dsfsdfsdfs']));
});

//$router->group(['namespace' => 'Common','prefix' => 'common'], function() use ($router) {
//    $router->any("socket","FsoketOpenController@socket");
//    $router->any("test","FsoketOpenController@test");
//});
