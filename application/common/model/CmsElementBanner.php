<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/12/22
 * Time: 17:10
 */

namespace app\common\model;


use think\Model;

class CmsElementBanner extends Model
{
    /**
     * @title 关联分组
     * @author vancens's a.qiang
     * @time 2020/12/26 15:07
     * @return \think\model\relation\HasOne
     */
    public function withType(){
        return $this->hasOne('CmsElementBannerType','id','type_id');
    }
}