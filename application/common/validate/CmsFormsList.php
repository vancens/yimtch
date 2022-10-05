<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2021/4/7
 * Time: 14:47
 */

namespace app\common\validate;


use think\Validate;

class CmsFormsList extends Validate
{
    protected $rule = [
        'name'              =>  'unique:forms_list'
    ];

    protected $message  =   [
        'name.unique'               => '表单名称重复'
    ];
}