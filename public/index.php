<?php
/**
 * Info: 入口文件
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/11/22
 * Time: 12:11
 */
namespace think;
require __DIR__ . '/../thinkphp/base.php';
Container::get('app')->run()->send();
