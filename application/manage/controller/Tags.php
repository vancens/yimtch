<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2021/5/21
 * Time: 22:49
 */

namespace app\manage\controller;


use app\common\controller\Base;
use app\common\model\CmsTagList;

class Tags extends Base
{
    /**
     * @title 列表
     * @author vancens's a.qiang
     * @time 2021/5/23 20:26
     * @return mixed
     */
    public function lists(){
        $data = (new \app\common\model\CmsTagList)
            ->withCount('withContent')
            ->all();
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 增加
     * @author vancens's a.qiang
     * @time 2021/5/23 16:17
     * @return mixed|\think\response\Json
     */
    public function add(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            $checkObj = new \app\common\validate\TagList();
            if (!$checkObj->check($par)){
                return ajaxReturnError($checkObj->getError());
            }
            //写入数据
            $ret = CmsTagList::create($par);
            return ajaxReturn($ret);

        }
        return $this->fetch();
    }

    /**
     * @title 修改
     * @author vancens's a.qiang
     * @time 2021/5/23 16:18
     * @return mixed|\think\response\Json
     */
    public function edit(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            $checkObj = new \app\common\validate\TagList();
            if (!$checkObj->check($par)){
                return ajaxReturnError($checkObj->getError());
            }
            //写入数据
            $ret = CmsTagList::update($par);
            return ajaxReturn($ret);

        }
        $id = $this->request->param('id');
        $data = CmsTagList::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 删除
     * @author vancens's a.qiang
     * @time 2021/5/23 16:18
     * @return \think\response\Json
     */
    public function del(){
        if ($this->request->isAjax()){
            $par = $this->request->param('id');
            $ret = CmsTagList::destroy($par);
            return ajaxReturn($ret);
        }
    }
}