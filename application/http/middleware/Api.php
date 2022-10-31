<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2022/10/30
 * Time: 20:47
 */

namespace app\http\middleware;

use think\facade\Url;

class Api
{

    /**
     * @title api请求验证
     * @author vancens's a.qiang
     * @time 2022/10/30 21:04
     * @param $request
     * @param \Closure $next
     * @return mixed|\think\response\Json
     */
    public function handle($request,\Closure $next)
    {
        if(!$request->isPost()){
            return ret_api(0,'请求信息错误',[]);
        }

        $token = $request->param('token');
        if (empty($token)){
            return ret_api(0,'请求信息错误',[]);
        }

        if (config('diy.http_token') !== $token){

            return ret_api(0,'请求信息验证错误',[]);
        }
        return $next($request);
    }
}