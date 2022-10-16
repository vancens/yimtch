<?php

namespace app\http\middleware;

use think\facade\Url;

class Check
{
    public function handle($request, \Closure $next)
    {
        //程序部署到虚拟主机环境
        if (config('siteinfo.is_virtual_mode') == true){
            //修改Url类的root函数值
            Url::root('/');

        }
        return $next($request);
    }
}
