<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/12/5
 * Time: 16:53
 */

namespace app\common\model;


use think\facade\Request;
use think\Model;


class CmsContentList extends Model
{

    /**
     * @var mixed
     */
    private $id;

    public function withColumn(){
        return $this->hasOne('CmsColumnList','id','column_id');
    }

    /**
     * @title 获取下一条内容
     * @author vancens's a.qiang
     * @time 2021/4/3 14:48
     * @param string $title
     * @param string $info
     * @return string
     */
    public function getNext($title='default',$info='default'){
        //文章ID
        $cid = Request::param('cid');
        if (!$cid){
            return "prenext标签适用于内容页";
        }
        //栏目ID
        $column_id = self::where('id',$cid)->value('column_id');
        $data =  self::where('id','>',$cid)
            ->where('column_id',$column_id)
            ->field('id,title')
            ->limit(1)
            ->find();
        if ($data){
            switch ($title){
                case 'default':
                    return "<a class='next' href='".url('index/Index/view',['cid'=>$data['id']])."'>{$data['title']}</a>";
                    break;
                case 'none':
                    return "<a class='next' href='".url('index/Index/view',['cid'=>$data['id']])."'></a>";
                    break;
            }
        }else{
            switch ($info){
                case 'default':
                    return "<a class='next'>暂无内容</a>";
                    break;
                case 'none':
                    return '';
                    break;
            }
        }
    }

    /**
     * @title 获取上一篇内容
     * @author vancens's a.qiang
     * @time 2021/4/3 14:48
     * @param string $title
     * @param string $info
     * @return string
     */
    public function getPre($title='default',$info='default'){
        $cid = Request::param('cid');
        if (!$cid){
            return "prenext标签适用于内容页";
        }
        //栏目ID
        $column_id = self::where('id',$cid)->value('column_id');
        $data =  self::where('id','<',$cid)
            ->where('column_id',$column_id)
            ->field('id,title')
            ->limit(1)
            ->order('id','desc')
            ->find();
        if ($data){
            switch ($title){
                case 'default':
                    return "<a class='prev' href='".url('index/Index/view',['cid'=>$data['id']])."'>{$data['title']}</a>";
                    break;
                case 'none':
                    return "<a class='prev' href='".url('index/Index/view',['cid'=>$data['id']])."'></a>";
                    break;
            }
        }else{
            switch ($info){
                case 'default':
                    return "<a class='prev'>暂无内容</a>";
                    break;
                case 'none':
                    return '';
                    break;
            }
        }
    }

    /**
     * @title 搜索页
     * @author vancens's a.qiang
     * @time 2021/4/3 20:43
     * @param $q
     * @param $row
     * @return CmsContentList[]|\think\Paginator
     * @throws \think\exception\DbException
     */
    public function search($q,$row){
        return self::field('id,title,title_short,thumb_pic,pic,source,author,read,seo_description,create_time')
            ->where('title','LIKE','%'.$q.'%')
            ->paginate($row);
    }

    /**
     * @title 根据ID获取数据
     * @author vancens's a.qiang
     * @time 2021/8/28 18:35
     * @param $ids
     * @param bool $addField
     * @return CmsContentList[]
     */
    public function tagGetWithid($ids,$addField=false){
        if ($addField == false){
            return self::field('id,title,title_short,thumb_pic,pic,source,author,read,seo_description,create_time')
                ->all($ids);
        }

        $ids_arr = explode(',',$ids);
        $column_id = self::where('id',$ids_arr[0])->value('column_id');
        $sign   = CmsColumnList::where('id',$column_id)->value('sign');
        $db_name = (new CmsColumnList())->getDbNameWithSign($sign);
        $addfield_arr = explode(',',$addField);
        $addfield_str = "";
        foreach ($addfield_arr as $value){
            $addfield_str .= ',b.'.$value;
        }
        return $this->alias('a')
            ->leftJoin($db_name.' b','a.id = b.aid')
            ->field('a.*'.$addfield_str)
            ->order('create_time','desc')
            ->where('id','in',$ids)
            ->select();

    }

    /**
     * @title 获取同栏目内容列表
     * @author vancens's a.qiang
     * @time 2022/3/25 4:17
     * @param $row
     * @param $order
     * @param $orderway
     * @return CmsContentList[]|array|\think\Collection
     */
    public function tagGetEqualList($row,$order,$orderway){
        $cid = Request::param('cid');
        if (empty($cid)){
            return [];
        }

        //得到栏目ID
        $column_id = self::where('id',$cid)->value('column_id');
        if (empty($column_id)){
            return [];
        }

        return self::where('column_id',$column_id)
            ->limit($row)
            ->order($order,$orderway)
            ->select();
    }
}