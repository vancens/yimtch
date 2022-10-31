<?php
/**
 * Info:
 * Created by 盐城网诚信息科技有限公司(vancens.com)
 * User: 阿强(550096055@qq.com)
 * Date: 2020/12/23
 * Time: 16:11
 */

namespace app\manage\controller;


use app\common\controller\Base;
use think\Db;

class Upload extends Base
{
    /**
     * @title 获取配置表数据
     * @author vancens's a.qiang
     * @time 2020/11/21 20:14
     * @return array
     */
    protected function getConfigs()
    {
        return Db::name('frame_config_list')->column('value','identification');
    }

    /**
     * @title 图片上传|无缩略图
     * @author vancens's a.qiang
     * @time 2020/11/21 15:30
     * @return \think\response\Json
     */
    public function uploadImg()
    {
        if($this->request->isAjax()){
            $data = $this->getConfigs();

            $file = $this->request->file('file');
            $info = $file
                ->validate(['size'=>$data['file_image_size']*1024,'ext'=>$data['file_image_ext']])
                ->move('../public/upload/images');
            if($info){
                return json([
                    'code' => 1,
                    'msg' => str_replace("\\","/",'/upload/images/'.$info->getSaveName()),
                ]);
            }else{
                return json([
                    'code' => 0,
                    'msg' => $file->getError()
                ]);
            }
        }
    }

    /**
     * @title 图片上传|附加缩略图返回
     * @author vancens's a.qiang
     * @time 2020/12/7 21:56
     * @return \think\response\Json
     */
    public function uploadThumbImg()
    {
        if($this->request->isAjax()){
            $data = $this->getConfigs();

            $file = $this->request->file('file');
            $info = $file
                ->validate(['size'=>$data['file_image_size']*1024,'ext'=>$data['file_image_ext']])
                ->move('../public/upload/images');

            if($info){
                //原图地址|绝对定位
                $pic_address = str_replace("\\","/",'/upload/images/'.$info->getSaveName());
                //原图地址|相对定位
                $pic_address_x = '.'.$pic_address;
                //获取图片的日期路径
                $pic_date = dirname($info->getSaveName());
                //获取图片名称
                $pic_name = $info->getFilename();
                //缩略图保存地址与名称|绝对定位
                $thumb_root = "/upload/thumbnail/{$pic_date}/{$pic_name}";
                //缩略图保存地址与名称|相对定位
                $thumb_root_x = ".{$thumb_root}";
                //缩略图保存路径
                $thumb_address = "./upload/thumbnail/{$pic_date}";
                //判断缩略图保存的路径是否存在
                if (!is_dir($thumb_address)){
                    mkdir($thumb_address);
                }
                //生成缩略图
                $image = \think\Image::open($pic_address_x);
                $image->thumb($data['thumb_width'], $data['thumb_width'])->save($thumb_root_x);
                return json([
                    'code' => 1,
                    'msg' => $pic_address,
                    't' => $thumb_root
                ]);
            }else{
                return json([
                    'code' => 0,
                    'msg' => $file->getError()
                ]);
            }
        }
    }


    /**
     * @title 上传视频
     * @author vancens's a.qiang
     * @time 2020/12/9 0:13
     * @return \think\response\Json
     */
    public function uploadVideo()
    {
        if($this->request->isAjax()){
            $data = $this->getConfigs();

            $file = $this->request->file('file');
            $info = $file
                ->validate(['size'=>$data['file_video_size']*1024,'ext'=>$data['file_video_ext']])
                ->move('../public/upload/video');
            if($info){
                return json([
                    'code' => 1,
                    'msg' => str_replace("\\","/",'/upload/video/'.$info->getSaveName()),
                ]);
            }else{
                return json([
                    'code' => 0,
                    'msg' => $file->getError()
                ]);
            }
        }
    }

    /**
     * @title 上传附件
     * @author vancens's a.qiang
     * @time 2020/12/9 12:02
     * @return \think\response\Json
     */
    public function uploadFile()
    {
        if($this->request->isAjax()){
            $data = $this->getConfigs();

            $file = $this->request->file('file');
            $info = $file
                ->validate(['size'=>$data['file_size']*1024,'ext'=>$data['file_ext']])
                ->move('../public/upload/file');
            if($info){
                return json([
                    'code' => 1,
                    'msg' => str_replace("\\","/",'/upload/file/'.$info->getSaveName()),
                ]);
            }else{
                return json([
                    'code' => 0,
                    'msg' => $file->getError()
                ]);
            }
        }
    }
}