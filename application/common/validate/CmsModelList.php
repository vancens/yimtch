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

class CmsModelList extends Validate
{
    protected $rule = [
        'name'              =>  'unique:cms_model_list',
        'db_name'           =>  'alphaNum|unique:cms_model_list',
    ];

    protected $message  =   [
        'name.unique'               => '模型名称重复',
        'db_name.alphaNum'               => '模型名称格式错误',
        'db_name.unique'     => '附加表名称重复',
    ];
}