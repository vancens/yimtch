<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2022/11/10
 * Time: 12:01
 */

namespace app\index\controller;

use app\common\model\CmsColumnList;
use app\common\model\CmsContentList;
use app\common\model\CmsModelList;
use think\Controller;
use think\Db;
use think\facade\Config;
use think\facade\View;

class Wap extends Controller
{
    /**
     * @title 初始化
     * @author vancens's a.qiang
     * @time 2022/11/10 12:02
     */
    public function initialize()
    {
        //父级栏目Ids
        View::share('parents',[]);
    }

    /**
     * @title 首页
     * @author vancens's a.qiang
     * @time 2021/1/13 16:51
     * @return mixed
     */
    public function index()
    {
        return $this->fetch('index');
    }


    /**
     * @title 列表页|栏目页
     * @author vancens's a.qiang
     * @time 2021/1/26 11:02
     * @return mixed|void
     */
    public function lists()
    {
        //$lid = $this->request->param('lid');
        $sign = $this->request->param('sign');
        $page = $this->request->param('page');
        //dump($sign);
        //dump($page);
        //判断lid状态
        if (!$sign){
            return $this->error('栏目参数错误');
        }
        //读取栏目信息
        $infoColumn = CmsColumnList::where('sign',$sign)->find();
        if ($infoColumn == null){
            return $this->fetch('system/404');
        }
        $info = $infoColumn->toArray();
        $templete = '';
        switch ($info['type']){
            case 1:
                $templete = $info['list_html'];
                break;
            case 2:
                $templete = $info['single_html'];
                break;
            case 3:
                return $this->redirect($info['link_url']);
        }
        //获取当前栏目的父级栏目集
        $parents = (new CmsColumnList())->getParentSigns($info['pid']);
        //dump($parents);
        array_push($parents,$sign);


        $this->assign('lists',$info);
        $this->assign('parents',$parents);
        return $this->fetch($templete);
    }

    /**
     * @title 内容页
     * @author vancens's a.qiang
     * @time 2021/4/1 18:31
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function view()
    {
        $cid = $this->request->param('cid');
        //判断cid状态
        if (!$cid)
        {
            return $this->error('内容参数错误');
        }
        //读取内容
        $content = CmsContentList::with('withColumn')
            ->getOrFail($cid);
        $db_name = CmsModelList::where('id',$content['with_column']['model_id'])
            ->value('db_name');
        $db_fullname = Config::get('database.prefix')."cms_content_with_".$db_name;
        $content['with'] = Db::table($db_fullname)
            ->where('aid',$cid)
            ->findOrFail();

        //dump($content);
        $this->assign('view',$content);

        //获取当前栏目的父级栏目集
        $parents = (new CmsColumnList())->getParentSigns($content['with_column']['pid']);
        array_push($parents,$content['with_column']['sign']);
        $this->assign('parents',$parents);
        //dump($parents);

        return $this->fetch($content['with_column']['content_html']);
    }
}