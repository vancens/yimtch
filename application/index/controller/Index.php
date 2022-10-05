<?php
namespace app\index\controller;

use app\common\model\CmsColumnList;
use app\common\model\CmsContentList;
use app\common\model\CmsFormsData;
use app\common\model\CmsFormsList;
use app\common\model\CmsModelList;
use think\Controller;
use think\Db;
use think\facade\Config;
use think\facade\View;

class Index extends Controller
{
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

    /**
     * @title 搜索页
     * @author vancens's a.qiang
     * @time 2021/4/4 12:13
     * @return mixed
     */
    public function search()
    {
        $q = $this->request->param('q');
        if ($q){
            $this->assign('keywords',$q);
            return $this->fetch('search');
        }else{
            $this->error('请输入搜索内容');
        }
    }

    /**
     * @title 自定义表单处理
     * @author vancens's a.qiang
     * @time 2021/4/10 17:413
     */
    public function forms()
    {
        $par = $this->request->param();
        if (isset($par['forms_id'])){
            //获取当前自定义表单字段信息
            $mode = new CmsFormsList();
            $fields_arr = $mode->getFields($par['forms_id']);
            $par['content'] = [];
            //判断数据字段是否完整
            //组装数据
            foreach ($fields_arr as $value){
                if (!isset($par[$value])){
                    $this->error('表单字段不完整');
                }else{
                    array_push($par['content'],[$value=>$par[$value]]);
                }
            }
            //检测数据
            $validate = new \app\common\validate\CmsFormsData();
            if (!$validate->check($par)){
                $this->error($validate->getError());
            }
            //写入数据
            $ret = CmsFormsData::create($par);
            if ($ret){
                $this->success('提交成功');
            }else{
                $this->error('提交内容错误');
            }

        }else{
            $this->error('表单字段错误');
        }

    }

    public function tags()
    {

    }

    /**
     * @title 生成首页
     * @author vancens's a.qiang
     * @time 2021/4/19 14:19
     * @return \think\response\Json
     */
    public function createIndex(){
        if ($this->request->isAjax()){
            $type = $this->request->param('type');
            $ret = false;
            $msg = '';
            switch ($type){
                case 0:
                    if (file_exists('./index.html')){
                        $ret = unlink('./index.html');
                        $msg = $ret ? '操作成功,已删除index.html文件':'操作失败,删除index.html文件失败';
                    }else{
                        $msg = '当前已是动态模式';
                    }
                    break;
                case 1:
                    $html = $this->fetch('index')->getContent();
                    $ret = file_put_contents('./index.html',$html);
                    $msg = $ret ? '操作成功,已生成index.html文件':'操作失败,index.html生成失败';
                    break;
            }
            return ajaxReturnMsg($ret,$msg);
        }
    }

    public function createColumn(){
        if (!$this->request->isAjax()){
            return false;
        }

        $lid = $this->request->param('lid');
        //获取子栏目集
        
        //读取栏目信息
        $info = CmsColumnList::get($lid)->toArray();
        $templete = '';
        $path = $info['static_path'];
        switch ($info['type']){
            case 1:
                $templete = $info['list_html'];
                break;
            case 2:
                $templete = $info['single_html'];
                break;
            case 3:
                return false;
        }
        //获取当前栏目的父级栏目集
        $parents = (new CmsColumnList())->getParentIds($info['pid']);
        array_push($parents,$lid);
        $this->assign('lists',$info);
        $this->assign('parents',$parents);
        $html =  $this->fetch($templete)->getContent();
        if (!is_dir('.'.$path)){
            mkdir('.'.$path);
        }
        $ret = file_put_contents('.'.$path.'/index.html',$html);
        //$msg = $ret ? '操作成功,已生成index.html文件':'操作失败,index.html生成失败';
        return ajaxReturn($ret);
    }

}
