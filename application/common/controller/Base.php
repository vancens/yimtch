<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/13
 * Time: 4:35
 * Info: 后台管理基类控制器
 */

namespace app\common\controller;


use app\common\model\FrameAdminerAuth;
use app\common\model\FrameAdminerAuthAccessType;
use app\common\model\SystemRules;
use think\Controller;
use think\Db;
use think\facade\Session;

class Base extends Controller
{
    //管理员资料|array
    protected $_GLOBAL_ADMIN;

    /**
     * @title 后台管理控制器基类
     * @author vancens's a.qiang
     * @time 2020/11/21 20:14
     */
    protected function initialize(){
        //登录状态判断
        if(!Session::has('adminer_infos') ){
            $this->error('请先登录','Login/in');
        }
        $this->_GLOBAL_ADMIN = Session::get('adminer_infos');
        $this->assign('GLOBAL_ADMIN',$this->_GLOBAL_ADMIN);

        //当前权限判断
        $module = $this->request->module();
        $controller = $this->request->controller();
        $action = $this->request->action();
        $current_node = "{$module}/{$controller}/{$action}";
        if ($controller != 'Index' && $controller != 'Upload'){
            //用户组ID
            $type_id = $this->_GLOBAL_ADMIN['type'];
            //获取所有权限节点ID
            $auth_ids = FrameAdminerAuthAccessType::where('type_id',$type_id)->column('auth_id');
            //获取所有权限节点node数组
            $auth_arr = FrameAdminerAuth::where('id','in',$auth_ids)->column('node');

            if (!in_array(strtolower($current_node),array_map('strtolower',$auth_arr))){
                $this->error('没有访问权限');
            }
        }

    }

    /**
     * @title 根据主键ID删除数据
     * @author vancens's a.qiang
     * @time 2020/11/21 20:25
     * @return int|\think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function DeleteFromId(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            $ret =  Db::name($par['db'])->delete($par['id']);
            return ajaxReturn($ret);
        }else{
            return ajaxReturnError('请求参数错误');
        }
    }

}