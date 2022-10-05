<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/12/22
 * Time: 16:14
 */

namespace app\manage\controller;


use app\common\controller\Base;
use app\common\model\CmsElementBanner;
use app\common\model\CmsElementBannerType;
use app\common\model\CmsElementOnly;
use app\common\model\CmsElementPic;

class Element extends Base
{
    /**
     * @title 幻灯分组列表
     * @author vancens's a.qiang
     * @time 2020/12/26 15:25
     * @return mixed
     */
    public function bannerTypeList()
    {
        $mode = new CmsElementBannerType();
        $data = $mode->withCount('withBanner')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 增加banner组
     * @author vancens's a.qiang
     * @time 2020/12/23 12:56
     * @return mixed|\think\response\Json
     */
    public function bannerTypeAdd(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //验证
            $validate = new \app\common\validate\CmsElementBannerType();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            $r = CmsElementBannerType::create($par);
            return ajaxReturn($r);
        }
        return $this->fetch();
    }

    /**
     * @title 修改banner组
     * @author vancens's a.qiang
     * @time 2020/12/23 13:01
     * @return mixed|\think\response\Json
     */
    public function bannerTypeEdit(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //验证
            $validate = new \app\common\validate\CmsElementBannerType();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            $r = CmsElementBannerType::update($par);
            return ajaxReturn($r);
        }
        $id = $this->request->param('id');
        $data = CmsElementBannerType::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 删除banner分组
     * @author vancens's a.qiang
     * @time 2020/12/23 13:04
     * @return \think\response\Json
     */
    public function bannerTypeDelete(){
        if ($this->request->isAjax()){
            $id = $this->request->param('id');
            $ret = CmsElementBanner::where('type_id',$id)->find();
            if ($ret != null){
                return ajaxReturnError('当前分组下存在banner,无法删除');
            }
            $det = CmsElementBannerType::destroy($id);
            return ajaxReturn($det);
        }
    }

    /**
     * @title 当前分组下的幻灯数据
     * @author vancens's a.qiang
     * @time 2020/12/23 15:05
     * @return mixed
     */
    public function bannerList(){
        $tid = $this->request->param('tid');
        //当前分组下的幻灯数据
        $data = CmsElementBanner::where('type_id',$tid)->order('order')->all();
        $this->assign('data',$data);
        //当前分组信息
        $type = CmsElementBannerType::get($tid);
        $this->assign('type',$type);
        return $this->fetch();

    }

    /**
     * @title 增加banner
     * @author vancens's a.qiang
     * @time 2020/12/23 15:52
     * @return mixed|\think\response\Json
     */
    public function bannerAdd(){
        if ($this->request->isAjax()){
            $par = $this->request->post();
            //写入数据
            $r = CmsElementBanner::create($par);
            return ajaxReturn($r);
        }
        $tid = $this->request->param('tid');
        //当前分组信息
        $type = CmsElementBannerType::get($tid);
        $this->assign('type',$type);
        return $this->fetch();
    }

    /**
     * @title 修改banner
     * @author vancens's a.qiang
     * @time 2020/12/26 15:06
     * @return mixed|\think\response\Json
     */
    public function bannerEdit(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //写入数据
            $r = CmsElementBanner::update($par);
            return ajaxReturn($r);
        }
        $id = $this->request->param('id');
        $data = CmsElementBanner::with('withType')->get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 删除banner
     * @author vancens's a.qiang
     * @time 2020/12/26 15:14
     * @return \think\response\Json
     */
    public function bannerDelete(){
        if ($this->request->isAjax()){
            $id = $this->request->param('id');
            $det = CmsElementBanner::destroy($id);
            return ajaxReturn($det);
        }
    }

    /**
     * @title 唯一内容
     * @author vancens's a.qiang
     * @time 2020/12/26 20:04
     * @return mixed
     */
    public function onlyList(){
        $data = CmsElementOnly::all();
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 增加内容
     * @author vancens's a.qiang
     * @time 2020/12/26 21:18
     * @return mixed|\think\response\Json
     */
    public function onlyAdd(){
        if ($this->request->isAjax()){
            $par = $this->request->post();
            //验证
            $validate = new \app\common\validate\CmsElementOnly();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            $r = CmsElementOnly::create($par);
            return ajaxReturn($r);
        }
        return $this->fetch();
    }

    /**
     * @title 修改内容
     * @author vancens's a.qiang
     * @time 2020/12/26 21:18
     * @return mixed|\think\response\Json
     */
    public function onlyEdit(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //验证
            $validate = new \app\common\validate\CmsElementOnly();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            $r = CmsElementOnly::update($par);
            return ajaxReturn($r);
        }
        $id = $this->request->param('id');
        $data = CmsElementOnly::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 删除内容
     * @author vancens's a.qiang
     * @time 2020/12/26 21:19
     * @return \think\response\Json
     */
    public function onlyDelete(){
        if ($this->request->isAjax()){
            $id = $this->request->param('id');
            $det = CmsElementOnly::destroy($id);
            return ajaxReturn($det);
        }
    }

    /**
     * @title 单图管理列表
     * @author vancens's a.qiang
     * @time 2020/12/26 21:35
     * @return mixed
     */
    public function picList(){
        $data = CmsElementPic::all();
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 增加图片
     * @author vancens's a.qiang
     * @time 2020/12/26 21:35
     * @return mixed|\think\response\Json
     */
    public function picAdd(){
        if ($this->request->isAjax()){
            $par = $this->request->post();
            //验证
            $validate = new \app\common\validate\CmsElementPic();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            $r = CmsElementPic::create($par);
            return ajaxReturn($r);
        }
        return $this->fetch();
    }

    /**
     * @title 修改图片
     * @author vancens's a.qiang
     * @time 2020/12/26 21:36
     * @return mixed|\think\response\Json
     */
    public function picEdit(){
        if ($this->request->isAjax()){
            $par = $this->request->param();
            //验证
            $validate = new \app\common\validate\CmsElementPic();
            if (!$validate->check($par)){
                return ajaxReturnError($validate->getError());
            }
            //写入数据
            $r = CmsElementPic::update($par);
            return ajaxReturn($r);
        }
        $id = $this->request->param('id');
        $data = CmsElementPic::get($id);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * @title 删除图片
     * @author vancens's a.qiang
     * @time 2020/12/26 21:36
     * @return \think\response\Json
     */
    public function picDelete(){
        if ($this->request->isAjax()){
            $id = $this->request->param('id');
            $det = CmsElementPic::destroy($id);
            return ajaxReturn($det);
        }
    }
}