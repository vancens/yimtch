<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/11/28
 * Time: 19:42
 */

namespace app\common\model;


use think\Model;

class CmsModelField extends Model
{
    /**
     * @title 一对一关联字段表单类型
     * @author vancens's a.qiang
     * @time 2020/12/2 15:10
     * @return \think\model\relation\HasOne
     */
    public function withForm(){
        return $this->hasOne('CmsModelFieldForm','id','field_type_id');
    }

    /**
     * @title 一对一关联当前字段所属的模型信息
     * @author vancens's a.qiang
     * @time 2020/12/2 15:10
     * @return \think\model\relation\HasOne
     */
    public function withModel(){
        return $this->hasOne('CmsModelList','id','model_id');
    }

    /**
     * @title 根据ID获取字段信息
     * @author vancens's a.qiang
     * @time 2021/8/29 2:12
     * @param $id
     * @return CmsModelField
     */
    public function tagGetFieldById($id){
        $ret =  self::get($id)->toArray();
        //dump($ret);
        return explode(',',$ret['db_field_values']);
    }

}