<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2021/4/5
 * Time: 18:08
 */

namespace app\common\model;


use think\Model;

class CmsFormsList extends Model
{
    // 设置json类型字段
    protected $json = ['fields'];

    // 设置JSON数据返回数组
    protected $jsonAssoc = true;

    //自动时间戳
    protected $autoWriteTimestamp = true;

    public function withData(){
        return $this->hasMany('CmsFormsData','forms_id','id');
    }

    /**
     * @title 根据表单ID返回表单字段集
     * @author vancens's a.qiang
     * @time 2021/4/8 15:21
     * @param $id
     * @return array
     */
    public function getFields($id){
        $fields_arr = self::get($id);
        $arrs = [];
        foreach ($fields_arr['fields'] as $value){
            array_push($arrs,$value['field']);
        }
        return $arrs;
    }

}