<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

Route::resource('orders','order/index');
Route::resource('users','user/index');
Route::get('rank/:method','user/rank/:method');
Route::get('share/:method','order/share/:method');
Route::get('user/:method','user/index/:method');
Route::get('match/:method','user/match/:method');
Route::get('ad','user/ad/index');
Route::get('message/:method','user/message/:method');
Route::post('user/:method','user/index/:method');
Route::post('match/:method','user/match/:method');
Route::get('stock/:method','user/stock/:method');
Route::get('desert/:method','desert/index/:method');
Route::post('desert/:method','desert/index/:method');
//Route::resource('api/:version/:controller','api/:version.:controller');
// Route::resource('blogs','index/blog');
//return [
	// '__pattern__' => [
	// 	'name' => '\w+',
	// ],
	//'hello/[:name]' => 'index/index/hello',
	// '[hello]' => [
	// 	':id' => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
	// 	':name' => ['index/hello', ['method' => 'post']],
	// ],
	
	
//];

// 
