<?php

namespace app\http\middleware;

use think\facade\Url;

class Check
{
    /**
     * @title 中间件入口文件
     * @author vancens's a.qiang
     * @time 2022/10/16 14:09
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request,\Closure $next)
    {
        //程序部署到虚拟主机环境
        if (config('siteinfo.is_virtual_mode') == true){
            //修改Url类的root函数值
            Url::root('/');
        }
        return $next($request);
    }
}
