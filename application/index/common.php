<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2022/3/25
 * Time: 2:45
 */

/**
 * @title 返回内容页URL
 * @author vancens's a.qiang
 * @time 2022/3/25 2:46
 * @param $id 内容ID
 * @return string
 */
function viewUrl($id){
    return url('index/Index/view',['cid'=>$id]);
}