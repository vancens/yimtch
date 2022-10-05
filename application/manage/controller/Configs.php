<?php
/**
 * Info: 系统配置信息
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/11/14
 * Time: 16:41
 */

namespace app\manage\controller;

use app\common\controller\Base;
use app\common\model\FrameConfigList;
use app\common\model\FrameConfigType;

class Configs extends Base
{
    /**
     * @title 配置列表
     * @author vancens's a.qiang
     * @time 2020/11/21 20:16
     * @return mixed|\think\response\Json
     * @throws \Exception
     */
    public function configList(){
        if ($this->request->isAjax()){
            $data = $this->request->param('v');
            //dump($data);
            $mode = new FrameConfigList();
            $ret  = $mode->saveAll($data);
            return ajaxReturn($ret);
        }
        $model = new FrameConfigType();
        //使用with查询，不支持with字段的排序，改用retWithConfigList方法
        //$data  = $model->with('withConfig')->select();
        $data = $model->retWithConfigList();
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 增加配置
     * @author vancens's a.qiang
     * @time 2020/11/21 20:16
     * @return mixed|\think\response\Json
     */
    public function configAdd(){
        if ($this->request->isAjax()){
            $data = $this->request->param();

            //return '开始验证';
            $validate = new \app\common\validate\FrameConfigList();
            if (!$validate->check($data)){
                return ajaxReturnError($validate->getError());
            }

            //$r = FrameConfigList::create($data);
            $model =  new FrameConfigList();
            $r = $model->save($data);
            return ajaxReturn($r);
        }
        return $this->fetch();
    }

    /**
     * @title 修改配置
     * @author vancens's a.qiang
     * @time 2020/11/21 20:16
     * @return mixed|\think\response\Json
     */
    public function configEdit(){
        if ($this->request->isAjax()){
            $data = $this->request->param();
            $validate = new \app\common\validate\FrameConfigList();
            if (!$validate->check($data)){
                return ajaxReturnError($validate->getError());
            }
            $m = new FrameConfigList();
            $r = $m::update($data);
            return ajaxReturn($r);
        }

        $id = $this->request->param('cid');
        $r = FrameConfigList::get($id);
        $this->assign('data',$r);
        return $this->fetch('config_edit');
    }

    /**
     * @title 删除配置
     * @author vancens's a.qiang
     * @time 2020/12/27 15:00
     * @return \think\response\Json
     * @throws \Exception
     */
    public function configDelete(){
        if ($this->request->isAjax()){
            $id = $this->request->param('id');
            $ret =  FrameConfigList::get($id);
            if ($ret->is_system == 1){
                return ajaxReturnError('系统配置,无法删除');
            }
            $dret = $ret->delete();
            return ajaxReturn($dret);
        }
    }
}