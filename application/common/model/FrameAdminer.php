<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25
 * Time: 16:23
 */

namespace app\common\model;


use think\Model;

class FrameAdminer extends Model
{
    protected $autoWriteTimestamp = true;

    /**
     * @title 验证登录
     * @author vancens's a.qiang
     * @time 2020/11/7 12:51
     * @param $where
     * @return array|false
     */
    public function checkLogin($where){
        $ret = $this->where($where)->field('id,type,nickname,name')->find();
        if (empty($ret)){
            return false;
        }else{
            return $ret->toArray();
        }
    }

    /**
     * @title 关联分类
     * @author vancens's a.qiang
     * @time 2020/11/14 16:22
     * @return \think\model\relation\HasOne
     */
    public function withType(){
        return $this->hasOne('FrameAdminerType','id','type');
    }
}