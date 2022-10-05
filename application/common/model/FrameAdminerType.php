<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25
 * Time: 16:23
 */

namespace app\common\model;


use think\Model;

class FrameAdminerType extends Model
{

    /**
     * @title 关联权限节点
     * @author vancens's a.qiang
     * @time 2020/12/20 13:55
     * @return \think\model\relation\HasMany
     */
    public function withAuth(){
        return $this->hasMany('frame_adminer_auth_access_type','type_id','id');
    }
}