<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/12/19
 * Time: 17:21
 */

namespace app\common\model;


use think\Model;

class FrameAdminerAuthType extends Model
{
    /**
     * @title 关联权限节点表
     * @author vancens's a.qiang
     * @time 2020/12/20 12:15
     * @return \think\model\relation\HasMany
     */
    public function withAuth(){
        return $this->hasMany('FrameAdminerAuth','type','id');
    }
}