<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2021/5/23
 * Time: 16:15
 */

namespace app\common\model;


use think\Model;

class CmsTagList extends Model
{
    /**
     * @title 关联
     * @author vancens's a.qiang
     * @time 2021/5/23 20:21
     * @return \think\model\relation\HasMany
     */
    public function withContent(){
        return $this->hasMany('CmsTagAccess','tag_id','id');
    }
}