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

// 前台首页
Route::rule('/','index/Index/index');

// 版本历史
Route::rule('api/versionHistory','api/Version/versionHistory');

// 授权查询
Route::rule('api/domainQuery','api/Version/domainQuery');
// 在线更新
Route::rule('api/getUpgrade','api/Version/getUpgrade');

return [];
