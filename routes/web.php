<?php

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
/*getページ開くという意味　/はtopページ　
viewフォルダの中のwelcomeというファイルを開く
groupはurlをまとめるという意味*/

Route::group(['prefix' => 'admin'], function() {
    Route::get('news/create', 'Admin\NewsController@add');
    Route::get('news/create', 'Admin\NewsController@add')->middleware('auth');
    Route::post('news/create', 'Admin\NewsController@create')->middleware('auth');
    Route::get('news', 'Admin\NewsController@index')->middleware('auth'); // 追記
    Route::get('news/edit', 'Admin\NewsController@edit')->middleware('auth'); // 追記
    Route::post('news/edit', 'Admin\NewsController@update')->middleware('auth'); // 追記
    Route::get('news/delete', 'Admin\NewsController@delete')->middleware('auth');

    Route::get('profile/edit', 'Admin\ProfileController@edit');
    Route::get('profile/edit', 'Admin\ProfileController@edit')->middleware('auth');
    Route::post('profile/edit', 'Admin\ProfileController@update')->middleware('auth');
    Route::get('profile', 'Admin\ProfileController@index')->middleware('auth');
    Route::get('profile/doedit', 'Admin\ProfileController@doedit')->middleware('auth'); // 追記
    Route::post('profile/doedit', 'Admin\ProfileController@doupdate')->middleware('auth'); // 追記
    Route::get('profile/delete', 'Admin\ProfileController@delete')->middleware('auth');
});
//1/11課題分
/*3.「http://XXXXXX.jp/XXX というアクセスが来たときに、
 AAAControllerのbbbというAction に渡すRoutingの設定」を書いてみてください。*/
    //Route::get('XXX', 'AAAController@bbb');//

/*【応用】 前章でAdmin/ProfileControllerを作成し、edit Actionを追加しました。
 web.phpを編集してadmin/profile/edit にアクセスしたら ProfileController の edit Action
 に割り当てるように設定してください。*/

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
