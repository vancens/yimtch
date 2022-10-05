<?php


namespace app\manage\controller;


use app\common\model\FrameAdminer;
use think\Controller;
use think\facade\Config;
use think\facade\Session;

class Login extends Controller
{
    /**
     * @title 后台登录
     * @author vancens's a.qiang
     * @time 2020/11/7 12:56
     * @return array|mixed
     */
    public function in(){
        if($this->request->isAjax()){
            $d = $this->request->param();
            if (!captcha_check($d['captcha'])){
                return ajaxReturnError('验证码错误');
            }
            unset($d['captcha']);
            $modeladmin = new FrameAdminer();
            $ret = $modeladmin->checkLogin($d);
            if($ret){
                Session::set('adminer_infos',$ret);
                return ajaxReturnSuccess('登录成功');
            }else{
                return ajaxReturnError('登录失败');
            }
        }

        return $this->fetch('in');
    }

    /**
     * @title 退出登录
     * @author vancens's a.qiang
     * @time 2020/11/7 12:57
     */
    public function out(){
        Session::delete('adminer_infos');
        $this->success('退出成功','Login/in');
    }


}