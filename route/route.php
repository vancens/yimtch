<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::rule('/','index/Index/index');

//列表页
//Route::rule('list/:lid','index/index/lists');
//列表页2
//Route::rule('list/:sign','index/index/lists')->pattern(['sign'=>'((?!index|manage|view).)*']);


//内容页
Route::rule('view/<cid>','index/Index/view');
//搜索页
Route::rule('search/<q>','index/Index/search');
//百度UE编辑器
Route::rule('ueditor/index','\vancens\ueditor\Ueditor@index','GET|POST');

//会员
Route::rule('member/login','member/Login/login');
Route::rule('member/home/[:colid]','member/Index/home');

//

//栏目页（放置在最后）
Route::rule('<sign>/sc/<screen>','index/index/lists')->pattern(['screen' => '[\w\*\%]+']);
Route::rule('<sign>/<page>','index/index/lists')->pattern(['page' => '\d+']);
Route::rule('<sign>','index/index/lists')->pattern(['sign'=>'((?!index|manage|view|search|api).)*']);


return [];
