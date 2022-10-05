<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2021/4/7
 * Time: 17:53
 */

namespace app\common\model;


use think\Model;

class CmsFormsData extends Model
{
    //自动时间戳
    protected $autoWriteTimestamp = true;

    // 设置json类型字段
    protected $json = ['content'];

    // 设置JSON数据返回数组
    protected $jsonAssoc = true;
}