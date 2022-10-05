<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/11/22
 * Time: 12:11
 */

namespace app\common\model;


use think\Db;
use think\facade\Config;
use think\Model;

class CmsColumnList extends Model
{
    /**
     * @title 返回栏目列表
     * @author vancens's a.qiang
     * @time 2020/11/25 18:27
     * @return array
     */
    public function retTree(){
        $data = self::field('id,pid,name,order,list_child_show,sign,url,type')
            ->order('order','asc')
            ->withCount('withContent')
            ->all();
        //return $data->toArray();
        return $this->dataSort($data->toArray());
        //return $this->dataSortQuote($data->toArray());
    }

    /**
     * @title 关联模型
     * @author vancens's a.qiang
     * @time 2020/12/10 15:56
     * @return \think\model\relation\HasOne
     */
    public function withModel(){
        return $this->hasOne('CmsModelList','id','model_id');
    }

    public function withContent(){
        return $this->hasMany('CmsContentList','column_id','id');
    }

    /**
     * @title 根据PID返回子级栏目
     * @author vancens's a.qiang
     * @time 2021/8/17 22:01
     * @param $pid
     * @param int $count
     * @return CmsColumnList[]
     */
    public function getSon($pid,$count=0){
        if ($count == 0){
            return self::field('id,name,name_en,info,litpic,url,sign')
                ->where('pid',$pid)
                ->order('order')
                ->all();
        }else{
            return self::field('id,name,name_en,info,litpic,url,sign')
                ->where('pid',$pid)
                ->withCount('withContent')
                ->order('order')
                ->all();
        }

    }

    /**
     * @title 根据sign返回子栏目
     * @author vancens's a.qiang
     * @time 2021/4/22 20:18
     * @param $sign
     * @return CmsColumnList[]
     */
    public function getSonFromSign($sign){
        $pid = $this->getIdWithSign($sign);
        return self::field('id,name,name_en,info,litpic,url,sign')->where('pid',$pid)->order('order')->all();
    }

    /**
     * @title 获取同级栏目
     * @author vancens's a.qiang
     * @time 2021/2/3 22:14
     * @param $id
     * @return CmsColumnList[]
     */
    public function getEqual($id){
        //根据id获取此栏目的父级栏目
        //根据父级栏目id获取下级栏目

        $pid = self::where('id',$id)->value('pid');
        return $this->getSon($pid);
    }

    /**
     * @title 根据sign返回同级栏目
     * @author vancens's a.qiang
     * @time 2021/4/22 20:21
     * @param $sign
     * @return CmsColumnList[]
     */
    public function getEqualFromSign($sign){
        $id = $this->getIdWithSign($sign);
        $pid = self::where('id',$id)->value('pid');
        return $this->getSon($pid);
    }

    /**
     * @title 根据ID获取单个栏目
     * @author vancens's a.qiang
     * @time 2021/1/20 13:37
     * @param $id
     * @return CmsColumnList
     */
    public function getOne($id){
        return self::field('id,name,name_en,info,litpic')->get($id);
    }

    /**
     * @title 获取当前栏目的所有子栏目（包含子级的子级）
     * @author vancens's a.qiang
     * @time 2021/1/22 13:25
     * @param $id
     * @return array
     */
    public function getSonIds($id){
        $all = self::field('id,pid,name')->all();
        return $this->dataSortId($all->toArray(),$id);
    }

    /**
     * @title 返回当前栏目及子栏目的内容
     * @author vancens's a.qiang
     * @time 2021/8/30 20:53
     * @param int $column_id
     * @param int $row
     * @param string $flag
     * @param string $order
     * @param string $orderway
     * @param false $addField
     * @return CmsContentList[]
     */
    public function getContentById($column_id=0,$row=8,$flag='',$order='create_time',$orderway='asc',$addField=false){
        $where = [];
        //是否存在栏目ID
        if ($column_id){
            $arr = $this->getSonIds($column_id);
            array_unshift($arr,(int)$column_id);
            array_push($where,['column_id','in',$arr]);
        }
        //是否存在flag
        if ($flag){
            $flag_str = '';
            $flag_arr = explode(',',$flag);
            foreach ($flag_arr as $value){
                $flag_str .= "FIND_IN_SET({$value},flag) OR ";
            }
            array_push($where,['','exp',Db::raw(chop($flag_str,'OR '))]);
        }

        //当没有附加字段时，直接返回
        if ($addField == false){
            if ($order == 'rand'){
                return CmsContentList::where($where)
                    ->with(['withColumn'=>function($query){
                        $query->field('id,name,url');
                    }])
                    ->limit($row)
                    ->orderRand()
                    ->all();
            }
            return CmsContentList::where($where)
                ->with(['withColumn'=>function($query){
                    $query->field('id,name,url');
                }])
                ->limit($row)
                ->order($order,$orderway)
                ->all();
        }

        //附加字段处理
        //获取副表名
        $sign = self::where('id',$column_id)->value('sign');
        $db_name = $this->getDbNameWithSign($sign);
        //字段处理
        $addfield_arr = explode(',',$addField);
        $addfield_str = "";
        foreach ($addfield_arr as $value){
            $addfield_str .= ',b.'.$value;
        }
        if ($order == 'rand'){
            return (new CmsContentList)
                ->alias('a')
                ->leftJoin($db_name.' b','a.id = b.aid')
                ->field('a.*'.$addfield_str)
                ->limit($row)
                ->orderRand()
                ->where($where)
                ->all();
        }
        return (new CmsContentList)
            ->alias('a')
            ->leftJoin($db_name.' b','a.id = b.aid')
            ->field('a.*'.$addfield_str)
            ->limit($row)
            ->order($order,$orderway)
            ->where($where)
            ->all();



    }

    /**
     * @title 获取内容列表
     * @author vancens's a.qiang
     * @time 2022/3/25 1:59
     * @param int $pagesize
     * @param false $addfield
     * @param string $order
     * @param string $orderway
     * @param false $raworder
     * @return array
     */
    public function getContentLists($pagesize=10,$addfield=false,$order='id',$orderway='desc',$raworder=false){
        //dump(request()->param());
        //栏目列表页
        if (request()->has('sign')){
            $sign = request()->param('sign');
            $column_id = $this->getIdWithSign($sign);
            //组装where表达式
            $where = [];
            $arr = $this->getSonIds($column_id);
            array_unshift($arr,(int)$column_id);
            array_push($where,['column_id','in',$arr]);
            array_push($where,['is_hide','=',0]);
            //判断是否存在screen参数（附表字段条件）
            $screen = false;
            if (request()->has('screen')){
                $screen = request()->param('screen');
            }

            //如果没有附加字段和筛选参数
            if ($addfield == false && !$screen){
                return CmsContentList::where($where)
                    ->order($order,$orderway)
                    ->with(['withColumn'=>function($query){
                        $query->field('id,name,url');
                    }])
                    ->paginate($pagesize);
            }
            //如果没有附加字段、存在筛选参数
            if ($addfield == false && $screen){
                //dump($screen);
                return [];
            }


            //存在附加字段处理

            //获取副表名
            $db_name = $this->getDbNameWithSign($sign);
            //返回字段处理
            $addfield_arr = explode(',',$addfield);
            $addfield_str = "";
            foreach ($addfield_arr as $value){
                $addfield_str .= ',b.'.$value;
            }
            //如果存在附加字段、不存在筛选参数
            if ($addfield && !$screen){

                //排序字段
                if ($raworder == false){
                    return (new ContentList)
                        ->alias('a')
                        ->leftJoin($db_name.' b','a.id = b.aid')
                        ->field('a.*'.$addfield_str)
                        ->order($order,$orderway)
                        ->where($where)
                        ->paginate($pagesize);
                }

                return (new ContentList)
                    ->alias('a')
                    ->leftJoin($db_name.' b','a.id = b.aid')
                    ->field('a.*'.$addfield_str)
                    ->orderRaw($raworder)
                    ->where($where)
                    ->paginate($pagesize);

            }
            //如果存在附加字段、存在筛选参数
            if ($addfield && $screen){
                $screen_arr = explode('__',$screen);
                //dump($screen_arr);
                $fujia_where = [];
                foreach ($screen_arr as $value){
                    $value_arr = explode('_',$value);
                    $fujia_where['b.'.$value_arr[0]]=$value_arr[1];
                    //array_push($fujia_where,[$value_arr[0]=>$value_arr[1]]);
                }
                //dump($fujia_where);

                if ($raworder == false){
                    return (new CmsContentList)
                        ->alias('a')
                        ->Join($db_name.' b','a.id = b.aid')
                        ->field('a.*'.$addfield_str)
                        ->order($order,$orderway)
                        ->where($where)
                        ->where($fujia_where)
                        //->fetchSql(true)
                        ->paginate($pagesize);
                }

                return (new CmsContentList)
                    ->alias('a')
                    ->Join($db_name.' b','a.id = b.aid')
                    ->field('a.*'.$addfield_str)
                    ->orderRaw($raworder)
                    ->where($where)
                    ->where($fujia_where)
                    //->fetchSql(true)
                    ->paginate($pagesize);

            }
        }

        //搜索页
        if (request()->has('q')){
            $q = request()->param('q');
            return ContentList::where('title','LIKE','%'.$q.'%')
                ->order($order,$orderway)
                ->paginate($pagesize);
        }
        return [];
    }

    /**
     * @title 获取分页数据
     * @author vancens's a.qiang
     * @time 2022/3/25 1:12
     * @param int $pagesize
     * @return array|mixed
     */
    public function getContentPage($pagesize=10){
        if (request()->has('sign')){
            $column_sign = request()->param('sign');
            //获取当前栏目的ID
            $column_id = $this->getIdWithSign($column_sign);
            //$column_id = request()->param('lid');
            $where = [];
            //获取当前栏目的子栏目
            $arr = $this->getSonIds($column_id);
            array_unshift($arr,(int)$column_id);
            array_push($where,['column_id','in',$arr]);
            $page = request()->param('page/d', 1);
            $content =  CmsContentList::where($where)
                //->paginate($pagesize)
                ->paginate($pagesize,false,['type'=>'page\Bootstrap','page' => $page,'path' => '/'.$column_sign.'/[PAGE].html']);
            return $content->render();
        }
        if (request()->has('q')){
            $q = request()->param('q');
            $data = CmsContentList::where('title','LIKE','%'.$q.'%')->paginate($pagesize);
            return $data->render();
        }
        return [];
    }

    /**
     * @title 获取定位
     * @author vancens's a.qiang
     * @time 2021/1/28 18:14
     * @return string
     */
    public function getPosition()
    {
        $data_str='<a href="'.url('index/Index/index').'">主页</a>';
        //栏目ID
        $lid = request()->param('lid');
        $sign = request()->param('sign');
        //内容ID
        $cid = request()->param('cid');
        //$lid = false;

        if ($cid)
        {
            $lid = CmsContentList::where('id',$cid)->value('column_id');
        }

        if ($sign){
            $lid = $this->getIdWithSign($sign);
        }

        if ($lid)
        {
            //获取当前栏目
            $one = (self::field('id,pid,name,sign,url')->get($lid))->toArray();
            //获取所有栏目
            $all = (self::field('id,pid,name,sign,url')->all())->toArray();
            $data = $this->dataSortPosition($all,$one['pid']);
            array_push($data,$one);

            foreach ($data as $key=>$value){
                $data_str .= '<span>></span><a href="'.$value['url'].'">'.$value['name'].'</a>';
            }
            return $data_str;
        }
        //内容页调用

        return $data_str;
    }

    /**
     * @title 获取当前栏目的所有父级栏目
     * @author vancens's a.qiang
     * @time 2021/1/28 18:14
     * @param $data
     * @param $pid
     * @param int $close
     * @return array
     */
    private function dataSortPosition($data,$pid,$close=0){
        static $arr = [];
        if ($close == 0){
            $arr = [];
        }
        foreach ($data as $k=>$value){
            if ($value['id'] == $pid){
                array_unshift($arr,$value);
                unset($data[$k]);
                $this->dataSortPosition($data,$value['pid'],1);
            }
        }
        return $arr;
    }

    /**
     * @title 获取父级栏目集
     * @author vancens's a.qiang
     * @time 2021/2/3 23:28
     * @param $pid
     * @return array
     */
    public function getParentIds($pid){
        //获取所有栏目
        $all = (self::field('id,pid')->all())->toArray();
        return $this->parentSort($all,$pid);
    }

    /**
     * @title 获取你栏目sign集
     * @author vancens's a.qiang
     * @time 2021/7/31 18:24
     * @param $pid
     * @return array
     */
    public function getParentSigns($pid){
        //获取所有栏目
        $all = (self::field('id,pid,sign')->all())->toArray();
        //dump($all);
        return $this->parentSignSort($all,$pid);
    }

    /**
     * @title 获取父级栏目数据递归排序
     * @author vancens's a.qiang
     * @time 2021/2/3 23:28
     * @param $data
     * @param $pid
     * @param int $close
     * @return array
     */
    private function parentSort($data,$pid,$close=0){
        static $arr = [];
        if ($close == 0){
            $arr = [];
        }
        foreach ($data as $k=>$value){

            if ($value['id'] == $pid){
                //dump($value['id']);
                array_unshift($arr,$value['id']);
                unset($data[$k]);
                $this->parentSort($data,$value['pid'],1);
            }
        }
        return $arr;
    }

    /**
     * @title 递归-获取父栏目sign
     * @author vancens's a.qiang
     * @time 2021/7/31 18:25
     * @param $data
     * @param $pid
     * @param int $close
     * @return array
     */
    private function parentSignSort($data,$pid,$close=0){
        static $arr = [];
        if ($close == 0){
            $arr = [];
        }
        foreach ($data as $k=>$value){
            if ($value['id'] == $pid){
                //dump($value['id']);
                array_unshift($arr,$value['sign']);
                unset($data[$k]);
                $this->parentSignSort($data,$value['pid'],1);
            }
        }
        //dump($arr);
        return $arr;
    }

    /**
     * @title 递归排列数组
     * @author vancens's a.qiang
     * @time 2020/11/23 19:15
     * @param $data
     * @param int $pid
     * @param int $leavl
     * @return array
     */
    private function dataSort($data,$pid=0,$leavl=0){
        static $arr = [];
        foreach ($data as $k=>$value){
            if ($value['pid'] == $pid){
                $value['level'] = $leavl;
                array_push($arr,$value);
                unset($data[$k]);
                $this->dataSort($data,$value['id'],$leavl+1);
            }
        }
        return $arr;
    }

    /**
     * @title 使用引用方式（暂未用）
     * @author vancens's a.qiang
     * @time 2020/11/25 18:19
     * @param $data
     * @return array
     */
    private function dataSortQuote($data){
        //dump($data);
        //构建一个新的数组，新数组的key值是自己的主键id值
        $items = [];
        foreach ($data as $v){
            $items[$v['id']] = $v;
        }
        //dump($items);
        $tree = array();
        foreach ($items as $k=>$value){
            //判断当前数组$value的pid值是否在items中存在
            //不存在，则说明这个$value是个顶级元素
            //存在，则说明这个$value是个子元素
            if (isset($items[$value['pid']])){
                $items[$value['pid']]['son'][] = &$items[$k];
            }else{
                $tree[] = &$items[$k];
            }
        }
        dump($tree);
        //dump($items);
        //die;
        return $tree;
    }

    /**
     * @title 递归排列数组
     * @author vancens's a.qiang
     * @time 2021/1/25 16:54
     * @param $data
     * @param $pid
     * @param int $close
     * @return array
     */
    private function dataSortId($data,$pid,$close=0){
        static $arr = [];
        if ($close == 0){
            $arr = [];
        }
        foreach ($data as $k=>$value){
            if ($value['pid'] == $pid){
                array_push($arr,$value['id']);
                unset($data[$k]);
                $this->dataSortId($data,$value['id'],1);
            }
        }
        return $arr;
    }

    /**
     * @title 根据栏目标识返回栏目ID
     * @author vancens's a.qiang
     * @time 2021/4/22 18:21
     * @param $sign
     * @return mixed
     */
    private function getIdWithSign($sign){
        return self::where('sign',$sign)->value('id');
    }

    /**
     * @title 根据sign返回副表名称
     * @author vancens's a.qiang
     * @time 2021/8/17 22:58
     * @param $sign
     * @return string
     */
    public function getDbNameWithSign($sign){
        $model_id = self::where('sign',$sign)->value('model_id');
        $db_name = CmsModelList::where('id',$model_id)->value('db_name');
        return "content_with_".$db_name;
    }

    /**
     * @title 获取顶级和子级栏目
     * @author vancens's a.qiang
     * @time 2022/3/25 1:24
     * @return array
     */
    public function tagGetTopChild(){
        $data = self::field("id,sign,name,name_en,url,pid")
            ->where("is_hide",0)
            ->order("order")
            ->select()
            ->toArray();
        if (empty($data)){
            return [];
        }

        $retArr = [];
        //得到一级菜单
        foreach ($data as $k=>$v){
            if ($v['pid'] == 0){
                array_push($retArr,$v);
                unset($data[$k]);
            }
        }

        if (empty($retArr)){
            return [];
        }

        foreach ($retArr as $ka=>$va){
            $retArr[$ka]['child']= [];
            foreach ($data as $kb=>$vb){
                if ($vb['pid'] == $va['id']){
                    array_push($retArr[$ka]['child'],$vb);
                }
            }
        }
        return $retArr;

    }


}