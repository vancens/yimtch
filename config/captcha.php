<?php
/**
 * Info: 验证码配置信息
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/11/11
 * Time: 19:14
 */

return [
    // 验证码字体大小
    'fontSize'    =>    30,
    // 验证码位数
    'length'      =>    4,
    // 关闭验证码杂点
    'useNoise'    =>    true,
    //是否画混淆曲线
    'useCurve'    =>    false,
    //验证码图片高度，设置为0为自动计算
    'imageH'      =>    0,
    //验证码图片宽度，设置为0为自动计算
    'imageW'      =>    0,
    //验证成功后是否重置
    'reset'       =>    true
];