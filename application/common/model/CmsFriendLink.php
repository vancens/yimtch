<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2021/4/9
 * Time: 17:25
 */

namespace app\common\model;


use think\Model;

class CmsFriendLink extends Model
{
    //自动时间戳
    protected $autoWriteTimestamp = true;

    /**
     * @title 关联分类
     * @author vancens's a.qiang
     * @time 2021/8/24 11:33
     */
    public function withType(){
        return $this->hasOne('CmsFriendLinkType','id','type_id');
    }
}