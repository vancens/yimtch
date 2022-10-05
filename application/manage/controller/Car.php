<?php
/**
 * Info: 房车信息管理
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2021/6/13
 * Time: 16:04
 */

namespace app\manage\controller;


use app\common\controller\Base;
use think\Db;
use think\facade\Session;

class Car extends Base
{
    /**
     * @title 二手房车列表
     * @author vancens's a.qiang
     * @time 2021/6/14 11:43
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function lists(){
        $data = Db::name('car_list')
            ->alias('c')
            ->field('s.id as join_state_id,s.name as join_state_name,c.id,c.title,c.pic,c.thumb_pic,c.price,c.shenhe_state,c.create_time')
            ->leftJoin('car_state s','c.shenhe_state=s.id')
            ->order('update_time')
            ->select();
        //dump($data);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 增加二手房车
     * @author vancens's a.qiang
     * @time 2021/6/14 11:43
     * @return mixed
     */
    public function add(){
        if ($this->request->isAjax()){

            $par = $this->request->param();
            $par['shangpai_time'] = strtotime($par['shangpai_time']);
            $par['user_id'] = 0;
            $par['adminer_id'] = (Session::get('adminer_infos'))['id'];
            //return $par;
            $par['create_time'] = time();
            $par['update_time'] = time();
            //多选按钮值转为字符串存储
            foreach ($par as $key=>$value){
                if (is_array($value)){
                    $par[$key] = implode(',',$value);
                }
            }
            $ret = Db::name('car_list')->strict(false)->insert($par);
            return ajaxReturn($ret);
        }
        return $this->fetch();
    }

    /**
     * @title 修改二手房车
     * @author vancens's a.qiang
     * @time 2021/7/17 20:34
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function edit(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            $par['shangpai_time'] = strtotime($par['shangpai_time']);
            $par['update_time'] = time();
            $par['user_id'] = 0;
            $par['adminer_id'] = (Session::get('adminer_infos'))['id'];
            $par['shenhe_time'] = time();
            $par['shenhe_admin_id'] = $par['adminer_id'];
            //多选按钮值转为字符串存储
            foreach ($par as $key=>$value){
                if (is_array($value)){
                    $par[$key] = implode(',',$value);
                }
            }
            $ret = Db::name('car_list')->strict(false)->update($par);
            return ajaxReturn($ret);
        }
        $id = $this->request->param('id');
        $data = Db::name('car_list')->find($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 删除二手房车
     * @author vancens's a.qiang
     * @time 2021/6/14 11:43
     */
    public function del(){

    }

    /**
     * @title 二手房车分类信息
     * @author vancens's a.qiang
     * @time 2021/6/14 11:43
     */
    public function screenInfo(){

    }
}