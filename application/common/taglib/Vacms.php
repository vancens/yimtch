<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/11/18
 * Time: 1:45
 */

namespace app\common\taglib;

use think\template\TagLib;

class Vacms extends TagLib
{
    /**
     * 定义标签
     * @var array[]
     */
    protected $tags     =   [
        'config'    => ['attr'=>'name','close'=>0],
        'pic'       => ['attr'=>'id','close'=>0],
        'columnbody'=> ['attr'=>'id','close'=>0],
        'tdk'       => ['attr'=>'id','close'=>1],
        'column'    => ['attr'=>'type,id','close'=>1],
        'content'   => ['attr'=>'columnid,row,id,order,orderway','close'=>1],
        'viewcont'  => ['attr'=>'row,order,orderway','close'=>1],
        'banner'    => ['attr'=>'id','close'=>1],
        'onlycont'  => ['attr'=>'id','close'=>1],
        'position'  => ['attr'=>'','close'=>0],
        'lists'     => ['attr'=>'','close'=>1],
        'page'      => ['attr'=>'','close'=>0],
        'prenext'   => ['attr'=>'type','close'=>0],
        'links'     => ['attr'=>'','close'=>1],
        'loop'      => ['attr'=>'db,row,order,orderway','close'=>1]
    ];

    /**
     * @title 获取配置
     * @author vancens's a.qiang
     * @time 2020/11/18 1:49
     * @param $tag
     * @return string
     */
    public function tagConfig($tag){                  
        $parse = '<?php ';
        $parse .= ' $datatag = Db::name("frame_config_list")->where("identification","'.$tag['name'].'")->value("value");';
        $parse .= ' echo "$datatag";';
        $parse .= ' ?>';
        return $parse;
    }

    /**
     * @title 定位
     * @author vancens's a.qiang
     * @time 2021/1/28 13:52
     * @param $tag
     * @return string
     */
    public function tagPosition($tag){
        $parse  = '<?php ';
        $parse .= '$__data__ = model("cms_column_list")->getPosition();';
        $parse .= ' echo "$__data__";';
        $parse .= ' ?>';
        return $parse;
    }

    /**
     * @title 获取栏目
     * @author vancens's a.qiang
     * @time 2021/1/13 17:26
     * @param $tag
     * @param $content
     * @return string
     */
    public function tagColumn($tag,$content){
        if (empty($tag['count'])){
            $tag['count'] = 0;
        }
        $parse = '';
        switch ($tag['type']){
            //顶级栏目
            case 'top':
                $parse = '<?php ';
                $parse .= '$__data__ = Db::name("cms_column_list")->field("id,sign,name,name_en,url")->where("pid","0")->where("is_hide",0)->order("order")->select();';
                $parse .= ' ?>';
                $parse .= '{volist name="__data__" id="vo"}';
                $parse .= $content;
                $parse .= '{/volist}';
                break;
            //子栏目
            case 'son':
                if (!isset($tag['id']) && request()->has('sign')){
                    $sign = request()->param('sign');
                    $parse .= '{volist name=":model(\'CmsColumnList\')->getSonFromSign(\''.$sign.'\')" id="vo"}';
                    $parse .= $content;
                    $parse .= '{/volist}';
                }
                if (isset($tag['id'])){
                    $parse .= '{volist name=":model(\'CmsColumnList\')->getSon('.$tag['id'].','.$tag['count'].')" id="vo"}';
                    $parse .= $content;
                    $parse .= '{/volist}';
                }
                break;
            //同级栏目
            case 'equal':
                if (!isset($tag['id']) && request()->has('sign')){
                    $sign = request()->param('sign');
                    $parse .= '{volist name=":model(\'CmsColumnList\')->getEqualFromSign(\''.$sign.'\')" id="vo"}';
                    $parse .= $content;
                    $parse .= '{/volist}';
                }
                if (isset($tag['id'])){
                    $parse .= '{volist name=":model(\'CmsColumnList\')->getEqual('.$tag['id'].')" id="vo"}';
                    $parse .= $content;
                    $parse .= '{/volist}';
                }
                break;
            //单一栏目
            case 'one':
                $parse = '<?php ';
                $parse .= '$vo = Db::name("cms_column_list")->field("id,name,name_en,info,litpic,content,url,sign")->where("id","'.$tag['id'].'")->find();';
                $parse .= ' ?>';
                $parse .= $content;
                break;
            case 'topchild':
                $parse .= '{volist name=":model(\'CmsColumnList\')->tagGetTopChild()" id="vo"}';
                $parse .= $content;
                $parse .= '{/volist}';
        }
        return $parse;
    }

    /**
     * @title 获取内容
     * @author vancens's a.qiang
     * @time 2021/1/24 12:01
     * @param $tag
     * @param $content
     * @return string
     */
    public function tagContent($tag,$content){
        if (empty($tag['columnid'])){
            $tag['columnid'] = 0;
        }
        if (empty($tag['row'])){
            $tag['row'] = 8;
        }
        if (empty($tag['flag'])){
            $tag['flag'] = null;
        }
        if (empty($tag['order'])){
            $tag['order'] = 'create_time';
        }
        if (empty($tag['orderway'])){
            $tag['orderway'] = 'asc';
        }
        if (empty($tag['addfield'])){
            $tag['addfield'] = 0;
        }
        if (isset($tag['aid'])){
            $parse = '{volist name=":model(\'CmsContentList\')->tagGetWithid(\''.$tag['aid'].'\',\''.$tag['addfield'].'\')" id="vo"}';
        }else{
            $parse  = '{volist name=":model(\'CmsColumnList\')->getContentById('.$tag['columnid'].','.$tag['row'].',\''.$tag['flag'].'\',\''.$tag['order'].'\',\''.$tag['orderway'].'\',\''.$tag['addfield'].'\')" id="vo"}';
        }

        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    public function tagViewcont($tag,$content){
        if (empty($tag['row'])){
            $tag['row'] = 5;
        }
        if (empty($tag['order'])){
            $tag['order'] = 'create_time';
        }
        if (empty($tag['orderway'])){
            $tag['orderway'] = 'desc';
        }
        $parse  = '{volist name=":model(\'CmsContentList\')->tagGetEqualList('.$tag['row'].',\''.$tag['order'].'\',\''.$tag['orderway'].'\')" id="vo"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;

    }
    /**
     * @title 内容页获取上一篇和下一篇
     * @author vancens's a.qiang
     * @time 2021/4/3 14:37
     * @param $tag
     * @return string
     */
    public function tagPrenext($tag){
        $parse = '<?php ';
        if (empty($tag['type'])){
            $tag['type'] = 'next';
        }
        if (empty($tag['title'])){
            $tag['title'] = 'default';
        }
        if (empty($tag['info'])){
            $tag['info'] = 'default';
        }
        switch ($tag['type']){
            case 'next':
                $parse .= '$__data__ = model("cms_content_list")->getNext(\''.$tag['title'].'\',\''.$tag['info'].'\');';
                $parse .= ' echo "$__data__";';
                $parse .= ' ?>';
                break;
            case 'pre':
                $parse .= '$__data__ = model("cms_content_list")->getPre(\''.$tag['title'].'\',\''.$tag['info'].'\');';
                $parse .= ' echo "$__data__";';
                $parse .= ' ?>';
                break;
        }
        return $parse;
    }

    /**
     * @title 列表页获取内容
     * @author vancens's a.qiang
     * @time 2021/3/1 23:37
     * @param $tag
     * @param $content
     * @return string
     */
    public function tagLists($tag,$content){
        if (empty($tag['pagesize'])){
            $tag['pagesize'] = 10;
        }
        if (empty($tag['addfield'])){
            $tag['addfield'] = false;
        }
        if (empty($tag['order'])){
            $tag['order'] = 'create_time';
        }
        if (empty($tag['orderway'])){
            $tag['orderway'] = 'asc';
        }
        if (empty($tag['raworder'])){
            $tag['raworder'] = false;
        }

        $parse = <<<EOF
        {volist name=":model('CmsColumnList')->getContentLists(
                                                            '{$tag['pagesize']}',
                                                            '{$tag['addfield']}',
                                                            '{$tag['order']}',
                                                            '{$tag['orderway']}',
                                                            '{$tag['raworder']}'
                                                            )" id="vo"}
        {$content}
        {/volist}
EOF;

        return $parse;
    }

    /**
     * @title 获取分页数据
     * @author vancens's a.qiang
     * @time 2021/3/2 12:37
     * @param $tag
     * @return string
     */
    public function tagPage($tag){
        if (empty($tag['pagesize'])){
            $tag['pagesize'] = 10;
        }
        $parse = '<?php ';
        $parse .= '$__data__ = model("cms_column_list")->getContentPage('.$tag['pagesize'].');';
        $parse .= ' echo "$__data__";';
        $parse .= ' ?>';
        return $parse;
    }

    /**
     * @title 获取banner
     * @author vancens's a.qiang
     * @time 2021/1/13 17:49
     * @param $tag
     * @param $content
     * @return string
     */
    public function tagBanner($tag,$content){
        $parse = '<?php ';
        $parse .= '$__data__ = Db::name("cms_element_banner")->field("id,name,info,pic,link,target")->where("type_id","'.$tag['id'].'")->order("order")->select();';
        $parse .= ' ?>';
        $parse .= '{volist name="__data__" id="vo"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    /**
     * @title 获取唯一元素
     * @author vancens's a.qiang
     * @time 2022/3/25 4:02
     * @param $tag
     * @param $content
     * @return string
     */
    public function tagOnlycont($tag,$content){
        $parse = '<?php ';
        $parse .= '$onlycont = Db::name("cms_element_only")->where("id","'.$tag['id'].'")->find();';
        $parse .= ' ?>';
        $parse .= $content;
        return $parse;
    }

    /**
     * @title 获取单图地址
     * @author vancens's a.qiang
     * @time 2021/1/13 17:04
     * @param $tag
     * @return string
     */
    public function tagPic($tag){
        $parse = '<?php ';
        $parse .= ' $datatag = Db::name("cms_element_pic")->where("id","'.$tag['id'].'")->value("pic");';
        $parse .= ' echo "$datatag";';
        $parse .= ' ?>';
        return $parse;
    }

    /**
     * @title 获取友情链接
     * @author vancens's a.qiang
     * @time 2021/4/9 18:26
     * @param $tag
     * @param $content
     * @return string
     */
    public function tagLinks($tag,$content){
        $parse = '';
        if (!isset($tag['id'])){
            $parse  .= '{volist name=":model(\'CmsFriendLink\')->select()" id="vo"}';
        }else{
            $parse  .= '{volist name=":model(\'CmsFriendLink\')->where(\'type_id\','.$tag['id'].')->select()" id="vo"}';
        }
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    /**
     * @title 获取一个栏目的body内容
     * @author vancens's a.qiang
     * @time 2021/7/24 16:25
     * @param $tag
     * @return string
     */
    public function tagColumnbody($tag){
        $parse = '<?php ';
        $parse .= ' $datatag = Db::name("cms_column_list")->where("id","'.$tag['id'].'")->value("content");';
        $parse .= ' echo "$datatag";';
        $parse .= ' ?>';
        return $parse;
    }

    /**
     * @title 获取一个栏目的tdk信息
     * @author vancens's a.qiang
     * @time 2021/7/25 12:04
     * @param $tag
     * @param $content
     * @return string
     */
    public function tagTdk($tag,$content){
        $parse = '<?php ';
        $parse .= ' $tdk = Db::name("cms_column_list")->where("id","'.$tag['id'].'")->field("seo_title,seo_keywords,seo_description")->find();';
        $parse .= ' ?>';
        $parse .= $content;
        return $parse;
    }

    /**
     * @title 获取内容
     * @author vancens's a.qiang
     * @time 2021/1/24 12:01
     * @param $tag
     * @param $content
     * @return string
     */
    public function tagLoop($tag,$content){
        if (empty($tag['row'])){
            $tag['row'] = 8;
        }
        if (empty($tag['order'])){
            $tag['order'] = 'create_time';
        }
        if (empty($tag['orderway'])){
            $tag['orderway'] = 'asc';
        }
        $parse  = '{volist name=":db()->name(\''.$tag['db'].'\')->limit('.$tag['row'].')->order(\'id\',\'desc\')->select()" id="vo"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

}