<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2021/9/1
 * Time: 11:49
 */

namespace app\manage\controller;


use app\common\controller\Base;
use app\common\model\CmsColumnList;
use app\common\model\CmsModelField;
use app\common\model\CmsModelList;
use app\member\model\MemberModelField;
use app\member\model\MemberModelList;
use app\member\model\MemberType;
use think\Db;
use think\facade\Config;

class Member extends Base
{

    /**
     * @title 会员分组
     * @author vancens's a.qiang
     * @time 2021/9/1 14:32
     * @return mixed
     */
    public function typeList(){
        $mode = new MemberType();
        $data = $mode->withCount(['withMember','withModel'])->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 增加分组
     * @author vancens's a.qiang
     * @time 2021/9/1 14:40
     * @return mixed|\think\response\Json
     */
    public function typeAdd(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //验证
            $validate = new \app\member\validate\MemberType();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            $r = MemberType::create($par);
            return ajaxReturn($r);
        }
        return $this->fetch();
    }

    /**
     * @title 修改分组
     * @author vancens's a.qiang
     * @time 2021/9/1 14:40
     * @return mixed|\think\response\Json
     */
    public function typeEdit(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //验证
            $validate = new \app\member\validate\MemberType();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            $r = MemberType::update($par);
            return ajaxReturn($r);
        }
        //读取分组数据
        $id = $this->request->param('id');
        $data = MemberType::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 删除分组
     * @author vancens's a.qiang
     * @time 2021/9/1 14:40
     * @return \think\response\Json
     */
    public function typeDel(){
        if ($this->request->isAjax()){
            $id = $this->request->param('id');
            $ret = \app\member\model\Member::where('type_id',$id)->find();
            if ($ret != null){
                return ajaxReturnError('当前分组下存在banner,无法删除');
            }
            $det = MemberType::destroy($id);
            return ajaxReturn($det);
        }
    }

    /**
     * @title 分组下的会员信息
     * @author vancens's a.qiang
     * @time 2021/9/12 15:27
     * @return mixed
     */
    public function listsByType(){
        $tid = $this->request->param('tid');
        $mode = new \app\member\model\Member();
        $data = $mode
            ->where('type_id',$tid)
            ->with('withType')
            ->select();
        //分组下的会员列表
        $this->assign('data',$data);

        //分组信息
        $typeInfo = MemberType::get($tid);
        $this->assign('type',$typeInfo);
        return $this->fetch();
    }

    /**
     * @title 全部会员列表
     * @author vancens's a.qiang
     * @time 2021/9/1 15:02
     * @return mixed
     */
    public function lists(){
        $mode = new \app\member\model\Member();
        $data = $mode->with('withType')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }


    /**
     * @title 增加会员
     * @author vancens's a.qiang
     * @time 2021/9/1 15:02
     * @return mixed|\think\response\Json
     */
    public function add(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //验证
            $validate = new \app\member\validate\Member();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            //$r = \app\member\model\Member::create($par);
            //return ajaxReturn($r);
            if (!empty($par['create_time'])){
                $par['create_time'] = strtotime($par['create_time']);
            }else{
                $par['create_time'] = time();
            }

            $par['update_time'] = time();
            //多选按钮值转为字符串存储
            foreach ($par as $key=>$value){
                if (is_array($value)){
                    $par[$key] = implode(',',$value);
                }
            }
            Db::startTrans();
            try {
                //写入主表数据
                $content_save = Db::name('member')->strict(false)->insertGetId($par);
                //获取附加表名称
                $db_name = Db::name('member_model_list')->where('id',$par['type_model_id'])->field('db_name')->find();
                $db_name_full = Config::get('database.prefix').'member_with_'.$db_name['db_name'];
                $par['uid'] = $content_save;
                //写入附表数据
                Db::table($db_name_full)->strict(false)->insert($par);
                Db::commit();
                return ajaxReturnSuccess('添加成功');
            }catch (\Exception $e){
                Db::rollback();
                return ajaxReturnError($e->getMessage());
            }
        }
        $tid = $this->request->param('tid');
        $typeInfo = MemberType::get($tid);
        $this->assign('type',$typeInfo);
        return $this->fetch();
    }

    /**
     * @title 修改会员
     * @author vancens's a.qiang
     * @time 2021/9/12 20:44
     * @return mixed|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //验证
            $validate = new \app\member\validate\Member();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            //$r = \app\member\model\Member::update($par);
            //return ajaxReturn($r);
            if (!empty($par['create_time'])){
                $par['create_time'] = strtotime($par['create_time']);
            }
            //多选按钮值转为字符串存储
            foreach ($par as $key=>$value){
                if (is_array($value)){
                    if (count($value) > 1){
                        array_pop($value);
                        $par[$key] = implode(',',$value);
                    }else{
                        $par[$key] = "";
                    }

                }
            }
            Db::startTrans();
            try {
                Db::name('member')->strict(false)->update($par);
                Db::table($par['db_name'])->strict(false)->where('uid',$par['id'])->update($par);
                Db::commit();
                return ajaxReturnSuccess('更新成功');
            }catch (\Exception $e){
                Db::rollback();
                return ajaxReturnError($e->getMessage());
            }
        }
        //读取主表数据
        $id = $this->request->param('id');
        $data = \app\member\model\Member::get($id);

        if (!$data){
            $this->error('数据读取失败');
        }
        $this->assign('data',$data);

        //读取栏目数据
        $typeInfo = MemberType::with('withModel')->get($data['type_id']);
        $this->assign('type',$typeInfo);

        //读取附表数据
        $fubiao = Db::table($typeInfo['with_model']['db_name'])->where('uid',$id)->find();
        $this->assign('fubiao',$fubiao);
        return $this->fetch();
    }

    /**
     * @title 删除会员
     * @author vancens's a.qiang
     * @time 2021/9/1 15:02
     * @return \think\response\Json
     */
    public function del(){
        if ($this->request->isAjax()){
            $id = $this->request->param('id');


            //$det = \app\member\model\Member::destroy($id);
            //return ajaxReturn($det);
            Db::startTrans();
            try {
                //栏目ID
                $type_id = Db::name('member')->where('id',$id)->value('type_id');
                //模型ID
                $model_id  = Db::name('member_type')->where('id',$type_id)->value('model_id');
                if ($model_id){
                    //附加表名称
                    $db_name   = Db::name('member_model_list')->where('id',$model_id)->value('db_name');
                    $db_name   = Config::get('database.prefix').'member_with_'.$db_name;
                    Db::table($db_name)->where('uid',$id)->delete();
                }

                Db::name('member')->delete($id);
                Db::commit();
                return ajaxReturnSuccess('删除成功');

            }catch (\Exception $e){
                Db::rollback();
                return ajaxReturnError('删除失败');
            }
        }
    }

    /**
     * @title 会员资料详情
     * @author vancens's a.qiang
     * @time 2021/9/12 21:21
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function detail(){
        //读取主表数据
        $id = $this->request->param('id');
        $data = \app\member\model\Member::with('withType')->get($id);
        if (!$data){
            $this->error('数据读取失败');
        }

        $this->assign('data',$data);

        //读取栏目数据
        $typeInfo = MemberType::with('withModel')->get($data['type_id']);
        $this->assign('type',$typeInfo);

        //读取附表数据
        $fubiao = Db::table($typeInfo['with_model']['db_name'])->where('uid',$id)->find();
        $this->assign('fubiao',$fubiao);

        return $this->fetch();
    }


    /**
     * @title 会员模型列表
     * @author vancens's a.qiang
     * @time 2020/11/26 17:23
     * @return mixed|\think\response\Json
     * @throws \Exception
     */
    public function modelList(){
        $model = new MemberModelList();
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
            $validate = new \app\member\validate\MemberModelList();
            if (!$validate->check($data)){
                return ajaxReturnError($validate->getError());
            }
            //创建附加数据表
            $db_name = Config::get('database.prefix')."member_with_".$data['db_name'];
            $info = "(uid int(11) NOT NULL,PRIMARY KEY (uid)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='{$data['name']}会员模型附加表'";
            $sql_mode = new \vancens\mysql\MysqlManage();
            $sql_ret  = $sql_mode->createTable($db_name,$info);
            if (!$sql_ret['s']){
                return ajaxReturnError($sql_ret['c']);
            }
            //写入数据
            $r = MemberModelList::create($data);
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
            $validate = new \app\member\validate\MemberModelList();
            if (!$validate->check($data)){
                return ajaxReturnError($validate->getError());
            }
            $r = MemberModelList::update($data);
            return ajaxReturn($r);
        }

        $id = $this->request->param('cid');
        $r = MemberModelList::get($id);
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
            $data = MemberModelList::get($par);
            if ($data['is_system']){
                return ajaxReturnError('系统模型,无法删除');
            }
            //判断是否有栏目使用此模型
            $is_child = MemberType::where('model_id',$par)->find();
            if ($is_child){
                return ajaxReturnError('有会员分组使用此模型,无法删除');
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
        $model_info = MemberModelList::get($mid);
        //模型下的字段信息
        $ret = MemberModelField::where('model_id',$mid)
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
            $validate = new \app\member\validate\MemberModelField();
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
                    $data['db_field_length'] = null;
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
            $r = MemberModelField::create($data);
            return ajaxReturn($r);
        }

        $mid = $this->request->param('mid');
        $model_info = MemberModelList::get($mid);
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
            $validate = new \app\member\validate\MemberModelField();
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

            $r = MemberModelField::update($data);
            return ajaxReturn($r);
        }

        $id = $this->request->param('cid');
        $r = MemberModelField::with('withModel')->get($id);
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
            $data= MemberModelField::with('withModel')->get($par);
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