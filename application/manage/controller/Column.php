<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/11/22
 * Time: 11:55
 */

namespace app\manage\controller;


use app\common\controller\Base;
use app\common\model\CmsColumnList;
use think\facade\Env;
use think\facade\Url;

class Column extends Base
{
    /**
     * @title 栏目列表
     * @author vancens's a.qiang
     * @time 2020/11/25 21:45
     * @return mixed|\think\response\Json
     * @throws \Exception
     */
    public function columnList(){
        if ($this->request->isAjax()){
            $type = $this->request->param('at');
            switch ($type){
                //更新栏目伸缩状态
                case 'stretch':
                    $par = $this->request->param();
                    $r = CmsColumnList::update($par);
                    return ajaxReturn($r);
                //更新栏目排序
                case 'order':
                    $data = $this->request->param('v');
                    //附加更新栏目URL
                    foreach ($data as $key=>$value)
                    {
                        $data[$key]['url'] = url('index/Index/lists',['sign'=>$value['sign']]);
                    }
                    //dump($data);
                    //return false;
                    $mode = new CmsColumnList();
                    $ret  = $mode->saveAll($data);
                    return ajaxReturn($ret);
            }
            return ajaxReturnError('参数错误');
        }

        $data = new CmsColumnList();
        $r = $data->retTree();
        //dump($r);
        $this->assign('data',$r);
        return $this->fetch();
    }


    /**
     * @title 添加栏目
     * @author vancens's a.qiang
     * @time 2020/11/22 12:14
     * @return mixed|\think\response\Json
     */
    public function columnAdd(){
        if ($this->request->isAjax()){
            $data = $this->request->param();
            $r = CmsColumnList::create($data);
            return ajaxReturn($r);
        }
        $template = $this->getTemplate();
        $this->assign('template',$template);
        return $this->fetch();
    }

    /**
     * @title 修改栏目
     * @author vancens's a.qiang
     * @time 2020/11/23 14:40
     * @return mixed|\think\response\Json
     */
    public function columnEdit(){
        if ($this->request->isAjax()){
            $data = $this->request->param();
            //设置栏目链接
            $data['url'] = Url::build('index/Index/lists',['sign'=>$data['sign']]);
            if ($data['type'] == 3){
                $data['url'] = $data['link_url'];
            }
            $r = CmsColumnList::update($data);
            return ajaxReturn($r);
        }

        $id = $this->request->param('cid');
        $r = CmsColumnList::get($id);
        $this->assign('data',$r);
        $template = $this->getTemplate();
        $this->assign('template',$template);
        return $this->fetch('column_edit');
    }

    /**
     * @title 删除栏目
     * @author vancens's a.qiang
     * @time 2020/11/25 21:55
     * @return \think\response\Json
     */
    public function columnDelete(){
        if ($this->request->isAjax()){
            $par = $this->request->param('id');
            //判断是否有子栏目
            $is_child = CmsColumnList::where('pid',$par)->find();
            if ($is_child){
                return ajaxReturnError('当前栏目存在子栏目,无法删除');
            }
            $ret = CmsColumnList::destroy($par);
            return ajaxReturn($ret);
        }
    }

    /**
     * @title 返回前端模板文件列表
     * @author vancens's a.qiang
     * @time 2021/1/30 0:58
     * @return array
     */
    private function getTemplate(){
        $root = Env::get('root_path').'tpl'.DIRECTORY_SEPARATOR.'index'.DIRECTORY_SEPARATOR.'index';
        $scan = scandir($root);
        $template = [];
        foreach ($scan as $value){
            if (pathinfo($value,PATHINFO_EXTENSION) == 'html'){
                array_push($template,$value);
            }
        }
        return $template;
    }

}