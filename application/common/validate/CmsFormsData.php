<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2021/4/8
 * Time: 16:40
 */

namespace app\common\validate;


use think\Validate;

class CmsFormsData extends Validate
{
    protected $rule = [
        'forms_id' => 'require|token'
    ];
}