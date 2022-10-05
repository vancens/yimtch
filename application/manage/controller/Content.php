<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/12/5
 * Time: 12:53
 */

namespace app\manage\controller;


use app\common\controller\Base;
use app\common\model\CmsColumnList;
use app\common\model\CmsContentList;
use app\common\model\CmsModelList;
use think\Db;
use think\facade\Config;

class Content extends Base
{
    /**
     * @title 所有内容列表
     * @author vancens's a.qiang
     * @time 2020/12/12 14:54
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function contentAll()
    {
        $ret = Db::name('cms_content_list')
            ->alias('con')
            ->leftJoin('cms_column_list col','con.column_id = col.id')
            ->field('con.id,con.title,con.create_time,col.name as column_name')
            ->order('id','desc')
            ->select();
        $this->assign('data',$ret);
        return $this->fetch();
    }
    /**
     * @title 当前栏目下的内容列表
     * @author vancens's a.qiang
     * @time 2020/12/5 16:56
     * @return mixed
     */
    public function contentList()
    {
        //栏目ID
        $column_id = $this->request->param('cid');
        //获取此栏目的所有子栏目
        $model = new CmsColumnList();
        $child_ids = $model->getSonIds($column_id);
        array_unshift($child_ids,(int)$column_id);
        //获取栏目信息
        $column = CmsColumnList::get($column_id);
        //获取内容
        $ret = CmsContentList::where('column_id','in',$child_ids)->order('id','desc')->all();
        $this->assign('data',$ret);
        $this->assign('column',$column);
        return $this->fetch();
    }

    /**
     * @title 增加内容
     * @author vancens's a.qiang
     * @time 2020/12/9 12:57
     * @return mixed|\think\response\Json
     */
    public function contentAdd()
    {
        if ($this->request->isAjax()){
            $par = $this->request->param();

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
            //dump($par);
            //return;
            Db::startTrans();
            try {
                //写入主表数据
                $content_save = Db::name('cms_content_list')->strict(false)->insertGetId($par);
                //获取附加表名称
                $db_name = Db::name('cms_model_list')->where('id',$par['column_model_id'])->field('db_name')->find();
                $db_name_full = Config::get('database.prefix').'cms_content_with_'.$db_name['db_name'];
                $par['aid'] = $content_save;
                //写入附表数据
                Db::table($db_name_full)->strict(false)->insert($par);
                Db::commit();
                return ajaxReturnSuccess('添加成功');
            }catch (\Exception $e){
                Db::rollback();
                return ajaxReturnError($e->getMessage());
            }

        }

        //栏目ID
        $column_id = $this->request->param('cid');
        //获取栏目信息
        $column = CmsColumnList::get($column_id);
        $this->assign('column',$column);
        //文章阅读数
        $read_num = mt_rand(1,100);
        $read_str = Db::name('frame_config_list')->where('identification','content_read')->value('value');

        if (!empty($read_str)){
            $read_arr = explode('-',$read_str);
            $read_arr[0] = isset($read_arr[0])?$read_arr[0]:1;
            $read_arr[1] = isset($read_arr[1])?$read_arr[1]:100;
            $read_arr[1] = $read_arr[1] < $read_arr[0] ? $read_arr[0]+100:$read_arr[1];
            $read_num = mt_rand($read_arr[0],$read_arr[1]);

        }
        $this->assign('read',$read_num);
        return $this->fetch();
    }

    /**
     * @title 修改内容
     * @author vancens's a.qiang
     * @time 2020/12/10 16:10
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function contentEdit()
    {
        if ($this->request->isAjax()){
            $par = $this->request->post();

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
                Db::name('cms_content_list')->strict(false)->update($par);
                Db::table($par['db_name'])->strict(false)->where('aid',$par['id'])->update($par);
                Db::commit();
                return ajaxReturnSuccess('内容更新成功');
            }catch (\Exception $e){
                Db::rollback();
                return ajaxReturnError($e->getMessage());
            }
        }

        $id = $this->request->param('id');
        //获取主表信息
        $ret= Db::name('cms_content_list')->where('id',$id)->find();
        if (!$ret){
            $this->error('数据读取失败');
        }
        //栏目ID
        $column_id = $ret['column_id'];
        //获取栏目信息
        $column = CmsColumnList::with('withModel')->get($column_id);

        //获取附表信息
        $fubiao = Db::table($column['with_model']['db_name'])->where('aid',$id)->find();

        $this->assign('column',$column);
        $this->assign('data',$ret);
        $this->assign('fubiao',$fubiao);
        return $this->fetch();
    }

    /**
     * @title 删除内容
     * @author vancens's a.qiang
     * @time 2020/12/11 0:00
     * @return \think\response\Json
     */
    public function contentDelete()
    {
        if ($this->request->isAjax()){
            $id = $this->request->param('id');
            Db::startTrans();
            try {
                //栏目ID
                $column_id = Db::name('cms_content_list')->where('id',$id)->value('column_id');
                //模型ID
                $model_id  = Db::name('cms_column_list')->where('id',$column_id)->value('model_id');
                if ($model_id){
                    //附加表名称
                    $db_name   = Db::name('cms_model_list')->where('id',$model_id)->value('db_name');
                    $db_name   = Config::get('database.prefix').'cms_content_with_'.$db_name;
                    Db::table($db_name)->where('aid',$id)->delete();
                }

                Db::name('cms_content_list')->delete($id);
                Db::commit();
                return ajaxReturnSuccess('内容删除成功');

            }catch (\Exception $e){
                Db::rollback();
                return ajaxReturnError('删除内容失败');
            }
        }
    }

    /**
     * @title 批量删除内容
     * @author vancens's a.qiang
     * @time 2021/8/14 19:24
     * @return \think\response\Json
     */
    public function contentDeletes(){
        if ($this->request->isAjax()){
            $ids = $this->request->param('ids');
            //return $ids;
            //$ids_arr = explode(",",$ids);
            if (count($ids)<1){
                return ajaxReturnError('删除失败,请检查是否选择内容');
            }
            //return $ids_arr;
            Db::startTrans();
            try {
                foreach ($ids as $value){
                    //栏目ID
                    $column_id = Db::name('cms_content_list')->where('id',$value)->value('column_id');
                    //模型ID
                    $model_id  = Db::name('cms_column_list')->where('id',$column_id)->value('model_id');
                    if ($model_id){
                        //附加表名称
                        $db_name   = Db::name('cms_model_list')->where('id',$model_id)->value('db_name');
                        $db_name   = Config::get('database.prefix').'cms_content_with_'.$db_name;
                        Db::table($db_name)->where('aid',$value)->delete();
                    }

                    Db::name('cms_content_list')->delete($value);
                }

                Db::commit();
                return ajaxReturnSuccess('内容删除成功');

            }catch (\Exception $e){
                Db::rollback();
                return ajaxReturnError('删除内容失败');
            }
        }
    }
}