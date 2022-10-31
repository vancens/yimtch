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
use think\Exception;
use think\facade\Config;
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
     * @time 2022/10/30 18:27
     * @return mixed|\think\response\Json
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function systemUpdate(){

        $path = Env::get('root_path').'update'.DIRECTORY_SEPARATOR.'versions.txt';
        if (is_file($path)){
            $localVersions = file_get_contents($path);
        }else{
            $localVersions = '1.0.0';
        }

        // 客户端异步请求
        if ($this->request->isAjax()){

            if (!$this->request->has('type')){
                return ajaxReturnError('请求参数错误');
            }
            $type = $this->request->param('type');
            // 请求域名
            $requestDomain = Config::get('diy.http_domain');

            switch ($type){

                //远程获取cms系统版本信息
                case 'check_versions':
                    return $this->httpPost('/api/getUpgrade',['versions'=>$localVersions]);
                    break;

                //远程下载更新包
                case 'down_load':
                    $zip = $this->request->param('zip');
                    $name = $this->request->param('name');
                    // 文件远程下载地址
                    $zip_url = $requestDomain.$zip;
                    // 检测远程文件状态
                    $check_zip_state = get_headers($zip_url);


                    if (strpos($check_zip_state[0],'200') === FALSE){
                        return ajaxReturnError('远程升级包不存在,请联系管理员');
                    }

                    // 本地保存路径
                    $save_path = Env::get('root_path').'update'.DIRECTORY_SEPARATOR.'download';

                    if (!is_dir($save_path)){
                        mkdir($save_path,0777,true);
                    }
                    if (!is_dir($save_path)){
                        return ajaxReturnError('创建下载目录失败,请检查目录权限');
                    }
                    // 升级包本地文件路径（包含文件名称）
                    $save_full_path = $save_path.DIRECTORY_SEPARATOR.$name.'.zip';


                    // 开始下载
                    try {
                        copy($zip_url,$save_full_path);
                    }catch (\Exception $e){
                        return ajaxReturnError('升级文件下载失败,请联系管理员');
                    }


                    if (!is_file($save_full_path)){
                        return ajaxReturnError('检测不到升级文件包,请联系管理员');
                    }
                    // 下载远程升级包（.zip）
                    $zipObj = new \ZipArchive();
                    if ($zipObj->open($save_full_path) !== TRUE){
                        return ajaxReturnError('打开压缩包失败,请联系管理员');
                    }
                    // 解压并覆盖本地文件
                    if ($zipObj->extractTo(Env::get('root_path')) !== TRUE){
                        return ajaxReturnError('解压文件失败,请联系管理员');
                    }

                    $zipObj->close();
                    return ajaxReturnSuccess('升级成功,请刷新页面（CTRL+F5）');
                    break;
            }

            return ajaxReturnError('请求参数错误');
        }


        // 获取版本更新记录数据
        $data = $this->httpPost('/api/versionHistory');
        $this->assign('data',$data);
        $this->assign('local_versions',$localVersions);
        return $this->fetch();
    }
}