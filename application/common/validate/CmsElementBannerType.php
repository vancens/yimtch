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

class CmsElementBannerType extends Validate
{
    protected $rule = [
        'name'              =>  'unique:element_banner_type',
    ];

    protected $message  =   [
        'name.unique'               => '名称重复',
    ];
}