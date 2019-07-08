<?php

namespace app\common\Mapping;


use app\api\exception\ValidateException;

abstract class AbstractUpload
{
    abstract function singleUpload($fileStream, $prefix, $full);


    public function filePath($prefix = 'default')
    {
        return sprintf('%s/%s/', $prefix, date('Y-m-d'));
    }

    public function randomName(string $ext)
    {
        return sprintf("%s.%s", md5(uniqid(rand())), $ext);
    }

    public function isImage($ext)
    {
        $fileType = ['jpg', 'jpeg', 'gif', 'bmp', 'png', 'mp4', 'JPG', 'apk', 'wmv', 'mov'];
        if (!in_array($ext, $fileType)) {
            throw new ValidateException('图片格式错误');
        }
        return true;
    }


    public function multiArrange($img)
    {
        $i = 0;
        if (is_string($img['name'])) {
            //如果是单文件
            $files[$i] = $img;
        } elseif (is_array($img['name'])) {
            //如果是多文件
            foreach ($img['name'] as $key => $val) {
                $files[$i]['name'] = $img['name'][$key];
                $files[$i]['type'] = $img['type'][$key];
                $files[$i]['tmp_name'] = $img['tmp_name'][$key];
                $files[$i]['error'] = $img['error'][$key];
                $files[$i]['size'] = $img['size'][$key];
                $i++;
            }
        }
        return $files;
    }
}