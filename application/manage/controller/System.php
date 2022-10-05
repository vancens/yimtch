<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2021/4/11
 * Time: 19:44
 */

namespace app\manage\controller;


use app\common\controller\Base;
use app\common\model\CmsColumnList;
use think\facade\Env;

class System extends Base
{
    /**
     * @title 生成
     * @author vancens's a.qiang
     * @time 2021/4/11 10:07
     * @return mixed
     */
    public function createHtml(){
        //判断当前首页模式
        $is_index_html = file_exists('./index.html');
        $this->assign('is_index_html',$is_index_html);
        //栏目页数据
        $column_obj = new CmsColumnList();
        $column_tree = $column_obj->retTree();
        $this->assign('column_tree',$column_tree);
        return $this->fetch();
    }

    /**
     * @title 清理缓存
     * @author vancens's a.qiang
     * @time 2021/5/28 18:46
     * @return mixed
     */
    public function clearTmp(){
        if ($this->request->isAjax()){
            $type = $this->request->param('type');
            $state = false;
            switch ($type){
                case 'temp':
                    $path = Env::get('runtime_path').'temp';
                    $state = $this->deleteFiels($path);
                    break;

                case 'log':
                    $path = Env::get('runtime_path').'log';
                    $dirList = array_diff(scandir($path),array('.','..'));
                    foreach ($dirList as $value){
                        $state = $this->deleteFiels($path.DIRECTORY_SEPARATOR.$value);
                        if (!$state){
                            return ajaxReturnError('清理失败');
                        }
                        $state = rmdir($path.DIRECTORY_SEPARATOR.$value);
                    }
                    break;
            }
            return ajaxReturn($state);
        }

        return $this->fetch();
    }

    /**
     * @title 删除目录下的文件夹
     * @author vancens's a.qiang
     * @time 2021/5/29 19:15
     * @param $path
     * @return bool
     */
    private function deleteFiels($path){
        $state = false;
        $fileList = scandir($path);
        if (count($fileList) == 2){
            return true;
        }
        foreach ($fileList as $value){
            if ($value != '.' && $value != '..'){
                $state = unlink($path.DIRECTORY_SEPARATOR.$value);
            }
        }
        return $state;

    }

    /**
     * @title 系统更新
     * @author vancens's a.qiang
     * @time 2021/9/4 17:14
     * @return mixed
     */
    public function systemUpdate(){
        $path = Env::get('root_path').'update'.DIRECTORY_SEPARATOR.'versions.txt';
        if (is_file($path)){
            $localVersions = file_get_contents($path);
        }else{
            $localVersions = '1.0.0';
        }

        //客户端异步请求

        if ($this->request->isAjax()){
            if (!$this->request->has('type')){
                return ajaxReturnError('请求参数错误');
            }
            $type = $this->request->param('type');
            $host = "http://cloud.yimtch.com/api/Index/getUpgrade";

            switch ($type){
                //远程获取cms系统版本信息
                case 'check_versions':
                    $ret = file_get_contents($host."?versions=".$localVersions);
                    return json($ret);
                    break;
                //远程下载更新包
                case 'down_load':
                    $zip = $this->request->param('zip');
                    $name = $this->request->param('name');
                    //开始下载
                    $zip_url = "http://cms.yimtch.com".$zip;
                    //return json($zip_url);
                    $save_path = Env::get('root_path').'update'.DIRECTORY_SEPARATOR.'download';

                    if (!is_dir($save_path)){
                        mkdir($save_path,0777,true);
                    }
                    if (!is_dir($save_path)){
                        return ajaxReturnError('创建下载目录失败,请检查目录权限');
                    }
                    $save_full_path = $save_path.DIRECTORY_SEPARATOR.$name.'.zip';

                    $copy_ret = copy($zip_url,$save_full_path);
                    if (!$copy_ret){
                        return ajaxReturnError('升级文件下载失败,请联系管理员');
                    }

                    if (!is_file($save_full_path)){
                        return ajaxReturnError('检测不到升级文件包,请联系管理员');
                    }
                    //解压文件，替换服务器文件
                    $zipObj = new \ZipArchive();
                    if ($zipObj->open($save_full_path) !== TRUE){
                        return ajaxReturnError('打开压缩包失败,请联系管理员');
                    }
                    $zipObj->extractTo(Env::get('root_path'));
                    $zipObj->close();
                    return ajaxReturnSuccess('升级成功,请刷新页面');
                    //return json($copy_ret);
                    break;
            }

            return ajaxReturnError('ajax参数错误');
        }
        //dump($localVersions);
        $this->assign('local_versions',$localVersions);
        return $this->fetch();
    }
}