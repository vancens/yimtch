<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/11/14
 * Time: 22:36
 */

namespace app\common\model;


use think\Model;

class FrameConfigType extends Model
{
    /**
     * @title 关联分类
     * @author vancens's a.qiang
     * @time 2020/11/17 21:59
     * @return \think\model\relation\HasMany
     */
    public function withConfig(){
        return $this->hasMany('FrameConfigList','type_id','id');
    }

    /**
     * @title 输出带配置的二维数据（配置使用排序功能）
     * @author vancens's a.qiang
     * @time 2020/11/17 22:02
     * @return mixed
     */
    public function retWithConfigList(){
        $type_data = self::all();
        $type_data = $type_data->toArray();

        $config_data = FrameConfigList::order('order')->all();
        $config_data = $config_data->toArray();
        //dump($config_data);

        foreach ($type_data as $key=>$value){
            $type_data[$key]['with_config'] = [];
            foreach ($config_data as $key_c=>$value_c){
                if ($value_c['type_id'] == $value['id']){
                    array_push($type_data[$key]['with_config'],$value_c);
                }
            }
        }
        return $type_data;
    }
}