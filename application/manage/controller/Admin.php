<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/12/19
 * Time: 17:23
 */

namespace app\manage\controller;


use app\common\controller\Base;
use app\common\model\FrameAdminer;
use app\common\model\FrameAdminerAuth;
use app\common\model\FrameAdminerAuthAccessType;
use app\common\model\FrameAdminerAuthType;
use app\common\model\FrameAdminerType;
use think\Db;

class Admin extends Base
{
    /**
     * @title 管理员列表
     * @author vancens's a.qiang
     * @time 2020/12/19 19:22
     * @return mixed
     */
    public function adminerList(){
        $data = FrameAdminer::with('withType')->all();
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 增加管理员
     * @author vancens's a.qiang
     * @time 2020/12/19 19:22
     * @return mixed|\think\response\Json
     */
    public function adminerAdd(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //验证
            $validate = new \app\common\validate\FrameAdminerList();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            $r = FrameAdminer::create($par);
            return ajaxReturn($r);
        }
        return $this->fetch();
    }

    /**
     * @title 修改管理员
     * @author vancens's a.qiang
     * @time 2020/12/19 21:29
     * @return mixed|\think\response\Json
     */
    public function adminerEdit(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //验证
            $validate = new \app\common\validate\FrameAdminerList();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            $r = FrameAdminer::update($par);
            return ajaxReturn($r);
        }
        $id = $this->request->param('id');
        $data = FrameAdminer::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 分组
     * @author vancens's a.qiang
     * @time 2020/12/19 21:39
     * @return mixed
     */
    public function typeList(){
        $data = FrameAdminerType::all();
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 增加分组
     * @author vancens's a.qiang
     * @time 2020/12/19 22:18
     * @return mixed|\think\response\Json
     */
    public function typeAdd()
    {
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //验证
            $validate = new \app\common\validate\FrameAdminerType();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            Db::startTrans();
            try {
                //写入数据|adminer_type
                $r = Db::name('adminer_type')->strict(false)->insertGetId($par);
                if (isset($par['auth_id'])){
                    $access = [];
                    foreach ($par['auth_id'] as $value){
                        array_push($access,['auth_id'=>$value,'type_id'=>$r]);
                    }
                    //写入数据|adminer_auth_access_type
                    Db::name('adminer_auth_access_type')->insertAll($access);
                }
                Db::commit();
                return ajaxReturnSuccess('增加成功');
            }catch (\Exception $e){
                Db::rollback();
                return ajaxReturnError('增加失败,'.$e->getMessage());
            }
            return ajaxReturnError('参数错误');
        }
        //读取权限节点
        $auth = FrameAdminerAuthType::order('order')->with('withAuth')->select();
        $this->assign('auth',$auth);
        return $this->fetch();
    }

    /**
     * @title 修改分组
     * @author vancens's a.qiang
     * @time 2020/12/19 22:31
     * @return mixed|\think\response\Json
     */
    public function typeEdit(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //验证
            $validate = new \app\common\validate\FrameAdminerType();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            Db::startTrans();
            try {
                //写入数据|adminer_type
                Db::name('adminer_type')->strict(false)->update($par);
                //删除
                Db::name('adminer_auth_access_type')->where('type_id',$par['id'])->delete();

                if (isset($par['auth_id'])){
                    $access = [];
                    foreach ($par['auth_id'] as $value){
                        array_push($access,['auth_id'=>$value,'type_id'=>$par['id']]);
                    }
                    //写入数据|adminer_auth_access_type
                    Db::name('adminer_auth_access_type')->insertAll($access);
                }
                Db::commit();
                return ajaxReturnSuccess('修改成功');
            }catch (\Exception $e){
                Db::rollback();
                return ajaxReturnError('修改失败,'.$e->getMessage());
            }
            return ajaxReturnError('参数错误');
        }
        $id = $this->request->param('id');
        //读取分组信息
        $data = FrameAdminerType::get($id);
        $this->assign('data',$data);
        //读取分组所拥有的权限节点
        $have_auth = FrameAdminerAuthAccessType::where('type_id',$id)->column('auth_id');
        $this->assign('have_auth',$have_auth);
        //读取权限节点
        $auth = FrameAdminerAuthType::order('order')->with('withAuth')->select();
        $this->assign('auth',$auth);
        
        return $this->fetch();
    }

    /**
     * @title 删除分组
     * @author vancens's a.qiang
     * @time 2020/12/19 22:35
     * @return \think\response\Json
     */
    public function typeDelete(){
        if ($this->request->isAjax()){
            $type_id = $this->request->param('id');
            $ret = FrameAdminer::where('type',$type_id)->find();
            if ($ret != null){
                return ajaxReturnError('当前分组下有管理员,无法删除');
            }
            Db::startTrans();
            try {
                Db::name('adminer_type')->delete($type_id);
                Db::name('adminer_auth_access_type')->where('type_id',$type_id)->delete();
                Db::commit();
                return ajaxReturnSuccess('删除分组成功');
            }catch (\Exception $e){
                return ajaxReturnError('删除分组失败,'.$e->getMessage());
            }
            return ajaxReturnError('参数错误');
        }
    }

    /**
     * @title 权限节点分组|菜单一级栏目
     * @author vancens's a.qiang
     * @time 2020/12/20 1:35
     * @return mixed
     */
    public function authTypeList()
    {
        $mode = new FrameAdminerAuthType();
        $data = $mode->withCount('withAuth')->order('order')->all();
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 增加权限节点分组
     * @author vancens's a.qiang
     * @time 2020/12/21 9:40
     * @return mixed|\think\response\Json
     */
    public function authTypeAdd()
    {
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //验证
            $validate = new \app\common\validate\FrameAdminerAuthType();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            $r = FrameAdminerAuthType::create($par);
            return ajaxReturn($r);
        }
        return $this->fetch();
    }

    /**
     * @title 修改权限节点分组
     * @author vancens's a.qiang
     * @time 2020/12/21 9:41
     * @return mixed|\think\response\Json
     */
    public function authTypeEdit()
    {
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //验证
            $validate = new \app\common\validate\FrameAdminerAuthType();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            $r = FrameAdminerAuthType::update($par);
            return ajaxReturn($r);
        }
        $id = $this->request->param('id');
        $data = FrameAdminerAuthType::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 删除权限节点
     * @author vancens's a.qiang
     * @time 2020/12/21 9:46
     * @return \think\response\Json
     */
    public function authTypeDelete()
    {
        if ($this->request->isAjax()){
            $id = $this->request->param('id');
            $ret = FrameAdminerAuth::where('type',$id)->find();
            if ($ret != null){
                return ajaxReturnError('当前分组下存在权限节点,无法删除');
            }
            $det = FrameAdminerAuthType::destroy($id);
            return ajaxReturn($det);
        }
    }

    /**
     * @title 当前分组下的权限节点列表
     * @author vancens's a.qiang
     * @time 2020/12/20 0:30
     * @return mixed
     */
    public function authList()
    {
        if ($this->request->isAjax()){
            $data = $this->request->param('v');

            $mode = new FrameAdminerAuth();
            $ret  = $mode->saveAll($data);
            return ajaxReturn($ret);
        }

        $tid = $this->request->param('tid');
        //权限节点列表
        $data = FrameAdminerAuth::where('type',$tid)->with('withType')->order('order')->all();
        $this->assign('data',$data);
        //分组信息
        $type = FrameAdminerAuthType::get($tid);
        $this->assign('type',$type);
        return $this->fetch();
    }



    /**
     * @title 增加权限节点
     * @author vancens's a.qiang
     * @time 2020/12/20 0:37
     * @return mixed|\think\response\Json
     */
    public function authAdd()
    {
        if ($this->request->isAjax()){
            $par = $this->request->post();
            //验证
            $validate = new \app\common\validate\FrameAdminerAuth();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            $r = FrameAdminerAuth::create($par);
            return ajaxReturn($r);
        }
        //分组信息
        $tid = $this->request->param('tid');
        $type = FrameAdminerAuthType::get($tid);
        $this->assign('type',$type);
        return $this->fetch();
    }

    /**
     * @title 修改权限节点
     * @author vancens's a.qiang
     * @time 2020/12/20 1:23
     * @return mixed|\think\response\Json
     */
    public function authEdit()
    {
        if ($this->request->isAjax()){
            $par = $this->request->post();
            //验证
            $validate = new \app\common\validate\FrameAdminerAuth();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            $r = FrameAdminerAuth::update($par);
            return ajaxReturn($r);
        }

        $id = $this->request->param('id');
        $data = FrameAdminerAuth::with('withType')->get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 删除权限节点
     * @author vancens's a.qiang
     * @time 2020/12/20 1:32
     * @return \think\response\Json
     */
    public function authDelete()
    {
        if ($this->request->isAjax()){
            $id = $this->request->param('id');
            $det = FrameAdminerAuth::destroy($id);
            return ajaxReturn($det);
        }
    }



}