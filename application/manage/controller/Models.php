<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/12/5
 * Time: 12:38
 */

namespace app\manage\controller;


use app\common\controller\Base;
use app\common\model\CmsColumnList;
use app\common\model\CmsModelList;
use app\common\model\CmsModelField;
use think\facade\Config;

class Models extends Base
{
    /**
     * @title 模型列表
     * @author vancens's a.qiang
     * @time 2020/11/26 17:23
     * @return mixed|\think\response\Json
     * @throws \Exception
     */
    public function modelList(){
        $model = new CmsModelList();
        $r = $model->withCount('withField')->order('order')->select();
        $this->assign('data',$r);
        return $this->fetch();
    }

    /**
     * @title 增加模型
     * @author vancens's a.qiang
     * @time 2020/11/26 20:16
     * @return mixed|\think\response\Json
     */
    public function modelAdd(){
        if ($this->request->isAjax()){
            $data = $this->request->param();

            //验证
            $validate = new \app\common\validate\CmsModelList();
            if (!$validate->check($data)){
                return ajaxReturnError($validate->getError());
            }
            //创建附加数据表
            $db_name = Config::get('database.prefix')."content_with_".$data['db_name'];
            $info = "(aid int(11) NOT NULL,PRIMARY KEY (aid)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='{$data['name']}模型内容附加表'";
            $sql_mode = new \vancens\mysql\MysqlManage();
            $sql_ret  = $sql_mode->createTable($db_name,$info);
            if (!$sql_ret['s']){
                return ajaxReturnError($sql_ret['c']);
            }

            //写入数据
            $r = CmsModelList::create($data);
            return ajaxReturn($r);
        }
        return $this->fetch();
    }

    /**
     * @title 修改模型
     * @author vancens's a.qiang
     * @time 2020/11/27 14:52
     * @return mixed|\think\response\Json
     */
    public function modelEdit(){
        if ($this->request->isAjax()){
            $data = $this->request->param();

            //验证
            $validate = new \app\common\validate\CmsModelList();
            if (!$validate->check($data)){
                return ajaxReturnError($validate->getError());
            }
            $r = CmsModelList::update($data);
            return ajaxReturn($r);
        }

        $id = $this->request->param('cid');
        $r = CmsModelList::get($id);
        $this->assign('data',$r);
        return $this->fetch();
    }

    /**
     * @title 删除栏目模型
     * @author vancens's a.qiang
     * @time 2020/11/27 16:37
     * @return \think\response\Json
     * @throws \Exception
     */
    public function modelDelete(){
        if ($this->request->isAjax()){
            $par = $this->request->param('id');
            //判断是否是系统模型
            $data = CmsModelList::get($par);
            if ($data['is_system']){
                return ajaxReturnError('系统模型,无法删除');
            }
            //判断是否有栏目使用此模型
            $is_child = CmsColumnList::where('model_id',$par)->find();
            if ($is_child){
                return ajaxReturnError('有栏目使用此模型,无法删除');
            }
            //删除数据表
            $sql_mode = new \vancens\mysql\MysqlManage();
            $sql_ret  = $sql_mode->deleteTable($data['db_name']);
            if (!$sql_ret['s']){
                return ajaxReturnError($sql_ret['c']);
            }
            //删除数据
            $ret = $data->delete();
            return ajaxReturn($ret);
        }
    }

    /**
     * @title 模型字段信息
     * @author vancens's a.qiang
     * @time 2020/11/28 19:46
     * @return mixed
     */
    public function fieldList(){
        $mid = $this->request->param('mid');
        //模型信息
        $model_info = CmsModelList::get($mid);
        //模型下的字段信息
        $ret = CmsModelField::where('model_id',$mid)
            ->with('withForm')
            ->order('order')
            ->all();
        $this->assign('data_model',$model_info);
        $this->assign('data_field',$ret);
        return $this->fetch();
    }

    /**
     * @title 增加字段
     * @author vancens's a.qiang
     * @time 2020/12/1 14:53
     * @return mixed|\think\response\Json
     */
    public function fieldAdd(){

        if ($this->request->isAjax()){
            $data = $this->request->param();
            //验证
            $validate = new \app\common\validate\CmsModelField();
            if (!$validate->check($data)){
                return ajaxReturnError($validate->getError());
            }

            $db_name = $data['db_name'];
            $field_name = $data['db_field_name'];
            $field_type = explode(',',$data['form_type'])[1];
            $field_length = "({$data['db_field_length']})";
            switch ($field_type){
                case 'text':
                case 'int':
                case 'float':
                    $field_length = null;
                    $data['db_field_length'] = 255;
                    break;
            }
            //创建数据表字段
            $info = "{$field_type}{$field_length} COMMENT '{$data['name']}';";
            $sql_mode = new \vancens\mysql\MysqlManage();
            $sql_ret  = $sql_mode->createField($db_name,$field_name,$info);
            if (!$sql_ret['s']){
                return ajaxReturnError($sql_ret['c']);
            }
            //写入数据
            $data['field_type_id'] = explode(',',$data['form_type'])[0];
            $r = CmsModelField::create($data);
            return ajaxReturn($r);
        }

        $mid = $this->request->param('mid');
        $model_info = CmsModelList::get($mid);
        $this->assign('data_model',$model_info);
        return $this->fetch();
    }

    /**
     * @title 字段修改
     * @author vancens's a.qiang
     * @time 2020/12/4 9:01
     * @return mixed|\think\response\Json
     */
    public function fieldEdit(){
        if ($this->request->isAjax()){
            $data = $this->request->param();

            //验证
            $validate = new \app\common\validate\CmsModelField();
            if (!$validate->check($data)){
                return ajaxReturnError($validate->getError());
            }
            //对比现有与修改的数据
            //数据库字段名称、数据库字段类型（使用类别ID比较）、数据库字段长度、数据库字段备注（字段名称）
            //放弃对比

            //修改数据表字段
            $db_name = $data['db_name'];
            $field_name = $data['db_field_name'];
            $default_db_field_name = $data['default_db_field_name'];
            $field_type = explode(',',$data['form_type'])[1];
            $data['field_type_id'] = explode(',',$data['form_type'])[0];
            $field_length = "({$data['db_field_length']})";
            switch ($field_type){
                case 'text':
                case 'int':
                case 'float':
                    $field_length = null;
                    $data['db_field_length'] = null;
                    break;
            }
            //修改数据表字段
            $info = "{$field_type}{$field_length} COMMENT '{$data['name']}';";
            $sql_mode = new \vancens\mysql\MysqlManage();
            $sql_ret  = $sql_mode->editField($db_name,$default_db_field_name,$field_name,$info);
            if (!$sql_ret['s']){
                return ajaxReturnError($sql_ret['c']);
            }

            $r = CmsModelField::update($data);
            return ajaxReturn($r);
        }

        $id = $this->request->param('cid');
        $r = CmsModelField::with('withModel')->get($id);
        $this->assign('data',$r);
        return $this->fetch();
    }

    /**
     * @title 删除字段（及删除附加表中的字段）
     * @author vancens's a.qiang
     * @time 2020/12/4 9:21
     * @return \think\response\Json
     * @throws \Exception
     */
    public function fieldDelete(){
        if ($this->request->isAjax()){
            $par = $this->request->param('id');
            $data= CmsModelField::with('withModel')->get($par);
            //删除附加表中的字段
            $sql_mode = new \vancens\mysql\MysqlManage();
            $sql_ret  = $sql_mode->deleteField($data['with_model']['db_name'],$data['db_field_name']);
            if (!$sql_ret['s']){
                return ajaxReturnError($sql_ret['c']);
            }
            //删除数据
            $ret = $data->delete();
            return ajaxReturn($ret);
        }
    }
}