<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * @title ajax成功返回信息
 * @author vancens's a.qiang
 * @time 2020/11/7 13:14
 * @param $content
 * @return \think\response\Json
 */
function ajaxReturnSuccess($content)
{
    return json(['code' => 1, 'msg' => $content]);
}

/**
 * @title ajax失败返回信息
 * @author vancens's a.qiang
 * @time 2020/11/7 13:15
 * @param $content
 * @return \think\response\Json
 */
function ajaxReturnError($content)
{
    return json(['code' => 0, 'msg' => $content]);
}

/**
 * @title ajax返回信息
 * @author vancens's a.qiang
 * @time 2020/11/7 13:24
 * @param $content
 * @return \think\response\Json
 */
function ajaxReturn($content)
{
    if ($content){
        return json(['code'=>1,'msg'=>'操作成功']);
    }else{
        return json(['code'=>0,'msg'=>'操作失败']);
    }
}

/**
 * @title ajax返回信息带提示内容
 * @author vancens's a.qiang
 * @time 2021/4/11 19:26
 * @param $content
 * @param $msg
 * @return \think\response\Json
 */
function ajaxReturnMsg($content,$msg)
{
    if ($content){
        return json(['code'=>1,'msg'=>$msg]);
    }else{
        return json(['code'=>0,'msg'=>$msg]);
    }
}
/**
 * @title 将字符串解析为数组
 * @author vancens's a.qiang
 * @time 2020/12/6 17:02
 * @param $value
 * @return false|string[]
 */
function va_expload($value)
{
    return explode(',',$value);
}

/**
 * @title 判断是否是首页
 * @author vancens's a.qiang
 * @time 2021/1/27 12:33
 * @return bool
 */
function is_index()
{
    $action = request()->action();
    if ($action == 'index' || $action == 'createindex'){
        return true;
    }else{
        return false;
    }
}

/**
 * @title 判断是否是当前栏目
 * @author vancens's a.qiang
 * @time 2021/1/27 12:40
 * @param $s 栏目标识
 * @return bool
 */
function is_current($s)
{
    if (request()->has('sign')) {
        $sign = request()->param('sign');
        if ($s == $sign){
            return true;
        }else{
            return false;
        }
    }
    else {
        return false;
    }
}

/**
 * @title 当前栏目是否在父sign集中
 * @author vancens's a.qiang
 * @time 2021/7/31 18:28
 * @param $sign
 * @param $signs
 * @return bool
 */
function is_active($sign,$signs){
    if (in_array($sign,$signs)){
        return true;
    }else{
        return false;
    }
}

/**
 * @title 返回模板文件名
 * @author vancens's a.qiang
 * @time 2021/1/30 1:06
 * @param $value
 * @return string
 */
function ret_template_name($value)
{
    return basename($value,'.html');
}

/**
 * @title 返回首页链接
 * @author vancens's a.qiang
 * @time 2021/2/1 18:25
 * @return string
 */
function ret_link_index(){
    return url('index/Index/index');
}

/**
 * @title 表页筛选URL
 * @author vancens's a.qiang
 * @time 2021/8/7 17:54
 * @param $type
 * @param $value
 * @return string
 */
function screen_url($type,$value){
    $par = car_s_toarray();
    $sign = request()->param('sign');
    $par[$type] = $value;

    if ($value === 0 && count($par) == 1){
        //unset($par[$type]);
        return url("index/index/lists",['sign'=>$sign]);
    }
    if ($value === 0 && count($par) > 1){
        unset($par[$type]);
    }

    $data = "";
    foreach ($par as $key=>$val){
        $data .= $key.'_'.$val.'__';
    }
    if ($data){
        $data = rtrim($data,"__");
    }
    return url("index/index/lists",['sign'=>$sign,'screen'=>$data]);
}

/**
 * @title 解析screen
 * @author vancens's a.qiang
 * @time 2021/8/8 20:45
 * @return array
 */
function car_s_toarray(){
    $par = request()->param('screen');
    //dump($par);
    if (!$par){
        return [];
    }
    $par_str_arr = explode('__',$par);
    //dump($par_str_arr);
    $par_arr = [];

    foreach ($par_str_arr as $value){
        $arr = explode('_',$value);
        //dump($arr[1]);
//        if ($arr[1] == 0){
//            continue;
//        }
        $par_arr[$arr[0]] = $arr[1];
        //dump($par_arr);
    }
    //dump($par_arr);
    return $par_arr;
}

/**
 * @title 车辆列表筛选a的激活状态
 * @author vancens's a.qiang
 * @time 2021/8/8 21:12
 * @param $type
 * @param $value
 * @return bool
 */
function screen_active($type,$value){
    //return false;
    $par = car_s_toarray();
    //dump(request()->param());
    //dump($par);
//    if (!isset($par[$type])){
//        $par[$type] = 0;
//    }
    if (!isset($par[$type])){
        //return false;
        $par[$type] = 0;
    }
    if ($par[$type] === $value){
        //dump($type);
        //dump($value);
        return true;

    }
    return false;
}

/**
 * @title api信息返回
 * @author vancens's a.qiang
 * @time 2021/9/4 17:51
 * @param $code
 * @param string $msg
 * @param string $data
 * @return \think\response\Json
 */
function ret_api($code,$msg='',$data=[]){
    return json([
        'code'=>$code,
        'msg'=>$msg,
        'data'=>$data
    ]);
}
