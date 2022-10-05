<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2021/9/12
 * Time: 23:53
 */

namespace app\manage\controller;


use app\common\controller\Base;
use app\complain\model\ComplainStore;
use app\store\model\StoreAuthentication;
use app\store\model\StoreComplain;
use app\store\model\StoreList;
use app\store\model\StoreType;
use think\Db;

class Store extends Base
{
    /**
     * @title 店铺分类
     * @author vancens's a.qiang
     * @time 2021/9/13 0:46
     * @return mixed|\think\response\Json
     * @throws \Exception
     */
    public function typeList(){
        if ($this->request->isAjax()){
            $type = $this->request->param('at');
            switch ($type){
                //更新栏目伸缩状态
                case 'stretch':
                    $par = $this->request->param();
                    $r = StoreType::update($par);
                    return ajaxReturn($r);
                //更新栏目排序
                case 'order':
                    $data = $this->request->param('v');

                    $mode = new StoreType();
                    $ret  = $mode->saveAll($data);
                    return ajaxReturn($ret);
            }
            return ajaxReturnError('参数错误');
        }
        $data = new StoreType();
        $r = $data->retTree();
        $this->assign('data',$r);
        return $this->fetch();
    }

    /**
     * @title 增加店铺分类
     * @author vancens's a.qiang
     * @time 2021/9/13 0:53
     * @return mixed|\think\response\Json
     */
    public function typeAdd(){
        if ($this->request->isAjax()){
            $data = $this->request->param();
            $r = StoreType::create($data);
            return ajaxReturn($r);
        }

        return $this->fetch();
    }

    /**
     * @title 修改分类
     * @author vancens's a.qiang
     * @time 2021/9/13 1:04
     * @return mixed|\think\response\Json
     */
    public function typeEdit(){
        if ($this->request->isAjax()){
            $data = $this->request->param();
            $r = StoreType::update($data);
            return ajaxReturn($r);
        }

        $id = $this->request->param('cid');
        $r = StoreType::get($id);
        $this->assign('data',$r);
        return $this->fetch();
    }

    /**
     * @title 删除分类
     * @author vancens's a.qiang
     * @time 2021/9/13 1:07
     * @return \think\response\Json
     */
    public function typeDelete(){
        if ($this->request->isAjax()){
            $par = $this->request->param('id');
            //判断是否有子栏目
            $is_child = StoreType::where('pid',$par)->find();
            if ($is_child){
                return ajaxReturnError('当前分类下存在子分类,无法删除');
            }
            $ret = StoreType::destroy($par);
            return ajaxReturn($ret);
        }
    }

    /**
     * @title 店铺列表
     * @author vancens's a.qiang
     * @time 2021/9/13 14:12
     * @return mixed
     */
    public function storeList(){
        $data = StoreList::with(['withType','withStatus'])
            ->field('id,name,name_short,create_time,type_id,status_id')
            ->all();
        $this->assign('data',$data);
        //dump($data);
        return $this->fetch();
    }

    /**
     * @title 增加店铺
     * @author vancens's a.qiang
     * @time 2021/9/13 14:12
     * @return mixed|\think\response\Json
     */
    public function storeAdd(){
        if ($this->request->isAjax()){
            $data = $this->request->param();
            $data['create_time'] = time();
            $data['apply_time'] = time();
            //多选按钮值转为字符串存储
            foreach ($data as $key=>$value){
                if (is_array($value)){
                    if (count($value) > 1){
                        array_pop($value);
                        $data[$key] = implode(',',$value);
                    }else{
                        $data[$key] = "";
                    }

                }
            }

            Db::startTrans();
            try {
                //写入主表数据
                $content_save = Db::name('store_list')->strict(false)->insertGetId($data);
                //写入认证表
                $data['store_id'] = $content_save;
                Db::name('store_authentication')->strict(false)->insert($data);
                Db::commit();
                return ajaxReturnSuccess('添加成功');
            }catch (\Exception $e){
                Db::rollback();
                return ajaxReturnError($e->getMessage());
            }
        }
        return $this->fetch();
    }

    /**
     * @title 修改店铺
     * @author vancens's a.qiang
     * @time 2021/9/13 15:35
     * @return mixed|\think\response\Json
     */
    public function storeEdit(){
        if ($this->request->isAjax()){
            $data = $this->request->param();
            //return json($data);
            //多选按钮值转为字符串存储
            foreach ($data as $key=>$value){
                if (is_array($value)){
                    if (count($value) > 1){
                        array_pop($value);
                        $data[$key] = implode(',',$value);
                    }else{
                        $data[$key] = "";
                    }

                }
            }
            Db::startTrans();
            try {
                //更新主表数据
                if (!empty($data['create_time'])){
                    $data['create_time'] = strtotime($data['create_time']);
                }
                $data['audit_time'] = time();
                $data['audit_adminer'] = $this->_GLOBAL_ADMIN['id'];

                Db::name('store_list')
                    ->where('id',$data['id'])
                    ->strict(false)
                    ->update($data);

                //更新认证表
                $fubiao = [
                    'license_img'   => $data['license_img'],
                    'license_name'   => $data['license_name'],
                    'license_address'   => $data['license_address'],
                    'license_person'   => $data['license_person'],
                    'license_number'   => $data['license_number'],
                ];

                Db::name('store_authentication')
                    ->where('store_id',$data['store_id'])
                    ->strict(false)
                    ->update($fubiao);

                Db::commit();
                return ajaxReturnSuccess('修改成功');
            }catch (\Exception $e){
                Db::rollback();
                return ajaxReturnError($e->getMessage());
            }
        }

        $id = $this->request->param('id');
        //读取主表数据
        $r = StoreList::get($id);
        $this->assign('data',$r);
        //读取认证内容
        $fubiao = StoreAuthentication::where('store_id',$id)->find();
        if (!$fubiao){
            $fubiao = false;
        }
        $this->assign('fubiao',$fubiao);
        return $this->fetch();
    }


    /**
     * @title 删除店铺
     * @author vancens's a.qiang
     * @time 2021/9/13 16:40
     * @return \think\response\Json
     */
    public function storeDelete(){
        if ($this->request->isAjax()){
            $id = $this->request->param('id');
            Db::startTrans();
            try {

                Db::name('store_list')->where('id',$id)->delete();
                Db::name('store_authentication')->where('store_id',$id)->delete();

                Db::commit();
                return ajaxReturnSuccess('删除成功');

            }catch (\Exception $e){
                Db::rollback();
                return ajaxReturnError($e->getMessage());
            }
        }
    }

    /**
     * @title 店铺详情
     * @author vancens's a.qiang
     * @time 2021/9/13 17:20
     * @return mixed
     */
    public function storeDetail(){
        $id = $this->request->param('id');
        //读取主表数据
        $r = StoreList::with(['withType','withStatus','withMember'])->get($id);
        $this->assign('data',$r);
        //读取认证内容
        $fubiao = StoreAuthentication::where('store_id',$id)->find();
        $this->assign('fubiao',$fubiao);
        return $this->fetch();
    }

    /**
     * @title 店铺投诉信息
     * @author vancens's a.qiang
     * @time 2021/9/13 22:09
     * @return mixed
     */
    public function complainList(){
        $data = ComplainStore::all();
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function complainDeal(){

    }

    public function complainDelete(){

    }

}