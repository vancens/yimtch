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

class CmsFriendLinkType extends Model
{
    //自动时间戳
    protected $autoWriteTimestamp = true;

    public function withData(){
        return $this->hasMany('CmsFriendLink','type_id','id');
    }
}