<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/11/17
 * Time: 14:53
 */

namespace app\common\validate;


use think\Validate;

class CmsModelField extends Validate
{
    protected $rule = [
        'db_field_name'           =>  'alphaNum|checkFieldName|systemName'
    ];

    protected $message  =   [
        'db_field_name.alphaNum'            => '模型名称格式错误',
        'db_field_name.checkFieldName'      => '数据库字段名重复',
        'db_field_name.systemName'          => '字段名称与系统表字段重复',
    ];

    /**
     * @title 验证db_field_name字段是否为一（在同等model_id下）
     * @author vancens's a.qiang
     * @time 2020/12/1 15:42
     * @param $value
     * @param $rules
     * @param array $data
     * @return bool
     */
    protected function checkFieldName($value,$rules,$data=[]){
        $ret = true;
        //如果存在id为修改；否则为新增
        if (array_key_exists('id',$data)){
            $ret = \app\common\model\CmsModelField::where([
                'db_field_name'=>$data['db_field_name'],
                'model_id'     => $data['model_id']
            ])
                ->where('id','<>',$data['id'])
                ->find();
        }else{
            $ret = \app\common\model\CmsModelField::where([
                'db_field_name'=>$data['db_field_name'],
                'model_id'     => $data['model_id']
            ])
                ->find();
        }
        if ($ret){
            return false;
        }else{
            return true;
        }
    }

    /**
     * @title 检测字段名称是否与系统内容表字段重名
     * @author vancens's a.qiang
     * @time 2020/12/7 20:55
     * @param $value
     * @param $rules
     * @param array $data
     * @return bool
     */
    protected function systemName($value,$rules,$data=[]){
        $systemField = [
            'id',
            'column_id',
            'title',
            'title_short',
            'thumb_pic',
            'pic',
            'flag',
            'source',
            'author',
            'read',
            'is_hide',
            'is_jump',
            'jump_link',
            'seo_keywords',
            'seo_description',
            'create_time',
            'update_time',
            'aid',
            'column_model_id',
            'db_name'
        ];
        if (in_array($data['db_field_name'],$systemField)){
            return false;
        }else{
            return true;
        }
    }
}