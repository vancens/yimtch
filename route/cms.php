<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2022/10/29
 * Time: 17:35
 */

//内容页
Route::rule('view/<cid>','index/Index/view');
//搜索页
Route::rule('search/<q>','index/Index/search');
//百度UE编辑器
Route::rule('ueditor/index','\vancens\ueditor\Ueditor@index','GET|POST');
//栏目页
Route::rule('h<sign>/sc/<screen>','index/index/lists')->pattern(['screen' => '[\w\*\%]+']);
Route::rule('h<sign>/<page>','index/index/lists')->pattern(['page' => '\d+']);
Route::rule('h<sign>','index/index/lists');