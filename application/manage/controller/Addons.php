<?php
/**
 * Info: 扩展管理
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2021/4/5
 * Time: 14:04
 */

namespace app\manage\controller;


use app\common\controller\Base;
use app\common\model\CmsFormsData;
use app\common\model\CmsFormsList;
use app\common\model\CmsFriendLink;
use app\common\model\CmsFriendLinkType;

class Addons extends Base
{
    /**
     * @title 自定义表单列表
     * @author vancens's a.qiang
     * @time 2021/4/7 17:59
     * @return mixed
     */
    public function formsList(){
        $mode = new CmsFormsList();
        $data = $mode->withCount('withData')->all();
        $this->assign('data',$data);
        return $this->fetch('forms_list');
    }

    /**
     * @title 增加自定义表单
     * @author vancens's a.qiang
     * @time 2021/4/7 14:50
     * @return mixed|\think\response\Json
     */
    public function formsAdd(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            if (count($par['field_sign']) != count(array_unique($par['field_sign']))){
                return ajaxReturnError('字段标识存在重复');
            }
            $field_len = count($par['field_sign']);
            $fields=[];
            for ($i=0; $i<$field_len; $i++){
                array_push($fields,['name'=>$par['field_name'][$i],'field'=>$par['field_sign'][$i]]);
            }
            $par['fields'] = $fields;
            //检查数据
            $check = new \app\common\validate\CmsFormsList();
            if (!$check->check($par)){
                return ajaxReturnError($check->getError());
            }
            //写入数据
            $ret = CmsFormsList::create($par);
            return ajaxReturn($ret);

        }
        return $this->fetch('forms_add');
    }

    /**
     * @title 自定义表单修改
     * @author vancens's a.qiang
     * @time 2021/4/7 17:46
     * @return mixed|\think\response\Json
     */
    public function formsEdit(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            if (count($par['field_sign']) != count(array_unique($par['field_sign']))){
                return ajaxReturnError('字段标识存在重复');
            }
            $field_len = count($par['field_sign']);
            $fields=[];
            for ($i=0; $i<$field_len; $i++){
                array_push($fields,['name'=>$par['field_name'][$i],'field'=>$par['field_sign'][$i]]);
            }
            $par['fields'] = $fields;
            //检查数据
            $check = new \app\common\validate\CmsFormsList();
            if (!$check->check($par)){
                return ajaxReturnError($check->getError());
            }
            //写入数据
            $ret = CmsFormsList::update($par);
            return ajaxReturn($ret);

        }
        $id = $this->request->param('id');
        $data = CmsFormsList::get($id);
        $this->assign('data',$data);
        return $this->fetch('forms_edit');
    }

    /**
     * @title 删除自定义表单
     * @author vancens's a.qiang
     * @time 2021/4/7 16:20
     * @return \think\response\Json
     */
    public function formsDelete(){
        if ($this->request->isAjax()){
            $par = $this->request->param('id');
            $ret = CmsFormsList::destroy($par);
            return ajaxReturn($ret);
        }
    }

    /**
     * @title 查看自定义表单数据
     * @author vancens's a.qiang
     * @time 2021/4/7 18:11
     * @return mixed
     */
    public function formsData(){
        $id = $this->request->param('id');
        $forms_info = CmsFormsList::get($id);
        $forms_data = CmsFormsData::where('forms_id',$id)->select();
        $this->assign('info',$forms_info);
        $this->assign('data',$forms_data);
        return $this->fetch('forms_data');
    }

    /**
     * @title 查看自定义表单数据(单个详情)
     * @author vancens's a.qiang
     * @time 2021/8/22 17:07
     * @return mixed
     */
    public function formsDataDetail(){
        $data_id = $this->request->param('id');
        $forms_data = CmsFormsData::get($data_id)->toArray();
        $forms_info = CmsFormsList::get($forms_data['forms_id']);
        //dump($forms_data);
        $this->assign('info',$forms_info);
        $this->assign('data',$forms_data);
        return $this->fetch('forms_data_detail');
    }

    /**
     * @title 友情链接分组
     * @author vancens's a.qiang
     * @time 2021/8/24 15:35
     * @return mixed
     */
    public function friendLinkType(){
        $lists = CmsFriendLinkType::order('order','asc')
            ->withCount('withData')
            ->all();
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    /**
     * @title 增加友情链接分组
     * @author vancens's a.qiang
     * @time 2021/8/24 15:36
     * @return mixed|\think\response\Json
     */
    public function friendLinkTypeAdd(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //写入数据
            $ret = CmsFriendLinkType::create($par);
            return ajaxReturn($ret);

        }
        return $this->fetch();
    }

    /**
     * @title 修改友情链接分组
     * @author vancens's a.qiang
     * @time 2021/8/24 15:39
     * @return mixed|\think\response\Json
     */
    public function friendLinkTypeEdit(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //写入数据
            $ret = CmsFriendLinkType::update($par);
            return ajaxReturn($ret);

        }
        $id = $this->request->param('id');
        $data = CmsFriendLinkType::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 删除友情链接
     * @author vancens's a.qiang
     * @time 2021/8/24 15:43
     * @return \think\response\Json
     */
    public function friendLinkTypeDelete(){
        if ($this->request->isAjax()){
            $par = $this->request->param('id');
            //判断当前分组是否是系统分组
            $isSystem = CmsFriendLinkType::where('id',$par)->value('is_system');
            if ($isSystem == 1){
                return ajaxReturnError('系统分组,无法删除');
            }

            //判断当前分组下是否有数据
            $hasData = CmsFriendLink::where('type_id',$par)->find();
            if ($hasData){
                return ajaxReturnError('当前分组下存在数据,无法删除');
            }
            $ret = CmsFriendLinkType::destroy($par);
            return ajaxReturn($ret);
        }
    }
    /**
     * @title 友情链接列表
     * @author vancens's a.qiang
     * @time 2021/4/9 17:56
     * @return mixed
     */
    public function friendLink(){
        $tid = $this->request->param('tid');

        $data = CmsFriendLink::order('order','asc')
            ->where('type_id',$tid)
            ->all();
        $this->assign('data',$data);

        //分组信息
        $type_info = CmsFriendLinkType::get($tid);
        $this->assign('type',$type_info);
        return $this->fetch();
    }

    /**
     * @title 增加友情链接
     * @author vancens's a.qiang
     * @time 2021/4/9 18:03
     * @return mixed|\think\response\Json
     */
    public function friendLinkAdd(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //写入数据
            $ret = CmsFriendLink::create($par);
            return ajaxReturn($ret);

        }
        $tid = $this->request->param('tid');
        $type_info = CmsFriendLinkType::get($tid);
        $this->assign('type',$type_info);
        return $this->fetch();
    }

    /**
     * @title 修改友情链接
     * @author vancens's a.qiang
     * @time 2021/4/9 18:03
     * @return mixed|\think\response\Json
     */
    public function friendLinkEdit(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //写入数据
            $ret = CmsFriendLink::update($par);
            return ajaxReturn($ret);

        }
        $id = $this->request->param('id');
        $data = CmsFriendLink::with('withType')->where('id',$id)->find();
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 删除友情链接
     * @author vancens's a.qiang
     * @time 2021/4/9 18:04
     * @return \think\response\Json
     */
    public function friendLinkDelete(){
        if ($this->request->isAjax()){
            $par = $this->request->param('id');
            $ret = CmsFriendLink::destroy($par);
            return ajaxReturn($ret);
        }
    }


}