<?php
namespace app\manage\controller;

use app\common\controller\Base;
use app\common\model\FrameAdminer;
use app\common\model\FrameAdminerAuthType;
use app\common\model\CmsColumnList;
use think\Db;
use think\facade\Config;
use think\facade\Env;

class Index extends Base
{
    /**
     * @author vancens's a.qiang
     * @title iframe框架
     * @time 2019/12/27 1:02
     * @return mixed
     */
    public function index(){
        //读取导航
        $nav = FrameAdminerAuthType::order('order')

            ->with(['withAuth'=>function($query){
                $query->order('order');
            }])
            ->all();
        $this->assign('nav',$nav);
        return $this->fetch();
    }

    /**
     * @title 控制台
     * @author vancens's a.qiang
     * @time 2021/5/4 19:15
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function kongzhitai(){
        //服务器信息
        $systemServer = $this->request->server();
        //dump($systemServer);
        //$serverInfo = explode(' ',$systemServer['SERVER_SOFTWARE']);
        $this->assign('serverInfo',$systemServer);

        //数据库信息
        $mysql_version = Db::query('SELECT VERSION() AS ver');
        $this->assign('sqlInfo',$mysql_version[0]['ver']);
        $this->assign('sqlType',Config::get('database.type'));

        //最新数据
        $new = Db::name('cms_content_list')
            ->alias('con')
            ->leftJoin('cms_column_list col','con.column_id = col.id')
            ->field('con.id,con.title,con.create_time,col.name as column_name')
            ->limit(6)
            ->select();
        $this->assign('new',$new);

        //数据统计
        $countNum = [];
        $countNum['content'] = Db::name('cms_content_list')->count();
        $countNum['column'] = Db::name('cms_column_list')->count();
        $countNum['tag'] = Db::name('cms_tag_list')->count();
        $countNum['forms'] = Db::name('cms_forms_list')->count();

        //系统版本
        $localVersions = '1.0.0';
        $path = Env::get('root_path').'update'.DIRECTORY_SEPARATOR.'versions.txt';
        if (is_file($path)){
            $localVersions = file_get_contents($path);
        }

        $this->assign('countnum',$countNum);
        $this->assign('systemV',$localVersions);

        return $this->fetch();
    }

    /**
     * @title 我的资料
     * @author vancens's a.qiang
     * @time 2020/11/9 9:32
     * @return mixed
     */
    public function my(){
        $mode = new FrameAdminer();
        $data = $mode->where('id',$this->_GLOBAL_ADMIN['id'])
            ->with('withType')
            ->find();
        $this->assign('data',$data);
        return $this->fetch('my');
    }



}
