<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/11/22
 * Time: 12:11
 */

namespace app\common\model;


use think\facade\Config;
use think\Model;

class CmsModelList extends Model
{
    /**
     * @title 修改器|获取DB名
     * @author vancens's a.qiang
     * @time 2020/12/27 17:29
     * @param $value
     * @return string
     */
    public function getDbNameAttr($value){
        return Config::get('database.prefix').'cms_content_with_'.$value;
    }

    /**
     * @title 关联字段
     * @author vancens's a.qiang
     * @time 2020/12/27 17:30
     * @return CmsModelList|\think\model\relation\HasMany
     */
    public function withField(){
        return $this->hasMany('CmsModelField','model_id','id');
    }

}