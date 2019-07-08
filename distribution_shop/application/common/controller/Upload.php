<?php


namespace app\common\controller;

use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;


class Upload
{
    protected $uploadManager;
    protected $BucketManager;
    protected $auth;
    private $token;
    protected $QiNiu_config = [
        'accesskey' => '',
        'secretkey' => '',
        'bucket' => '',
    ];

    public function __construct()
    {
        $this->uploadManager = new UploadManager();

        $this->auth = new Auth($this->QiNiu_config['accesskey'],$this->QiNiu_config['secretkey']);
        $this->token =  $this->auth->uploadToken($this->QiNiu_config['bucket']);

        $this->BucketManager = new BucketManager($this->auth);
    }



    public function single_upload($img,$prefix)
    {
        if(!$img){
            abort(400,'图片不能为空');
        }

        $ext = pathinfo($img['name'], PATHINFO_EXTENSION);
        $this->isImage($ext);
        $key = $this->file_path($ext,$prefix);
        list($ret, $err) = $this->uploadManager->putFile($this->token,$key,$img['tmp_name']);
        if ($err !== null) {
            return $err;
        } else {
            return $ret['key'];
        }

    }

    public function single_upload_stream($stream,$prefix='stream')
    {
        if(!$stream){
            abort(400,'图片不能为空');
        }

//        $ext = pathinfo($img['name'], PATHINFO_EXTENSION);
//        $this->isImage($ext);
        $key = $this->file_path('png',$prefix);
        list($ret, $err) = $this->uploadManager->put($this->token,$key,$stream);
        if ($err !== null) {
            return $err;
        } else {
            return $ret['key'];
        }

    }

    public function multi_arrange($img){

        $i=0;
        foreach($img as $key=>$file){

            //因为这时$_FILES是个三维数组，并且上传单文件或多文件时，数组的第一维的类型不同，这样就可以拿来判断上传的是单文件还是多文件
            if(is_string($file['name'])){
                //如果是单文件
                $files[$i]=$file;
                $i++;
            }elseif(is_array($file['name'])){
                //如果是多文件
                foreach($file['name'] as $key=>$val){
                    $files[$i]['name']=$file['name'][$key];
                    $files[$i]['type']=$file['type'][$key];
                    $files[$i]['tmp_name']=$file['tmp_name'][$key];
                    $files[$i]['error']=$file['error'][$key];
                    $files[$i]['size']=$file['size'][$key];
                    $i++;
                }
            }
        }
        return $files;
    }

    public function delete($key){

        $res = $this->BucketManager->delete($this->QiNiu_config['bucket'],$key);
        if($res !== null){
            abort(400,'该文件不存在或已被删除');
        }else{
            abort(200,'删除成功');
        }
    }

    private function file_path($ext,$prefix='default'){
        return sprintf('%s/%s/%s.%s',$prefix,date('Y-m-d'),md5(uniqid(rand())),$ext);
    }

    private function isImage($ext) {

        $filetype = ['jpg', 'jpeg', 'gif', 'bmp', 'png','mp4','JPG','apk','wmv','mov'];
        if (!in_array($ext, $filetype))
        {
            abort('400','图片格式错误');
        }
        return true;
    }



}