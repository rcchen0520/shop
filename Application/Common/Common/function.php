<?php
function removeXSS($val)
{
    static $obj = null;
    if($obj === null)
    {
        require('./HTMLPurifier/HTMLPurifier.includes.php');
        $config = HTMLPurifier_Config::createDefault();
        //保留a标签的target属性
        $config->set('HTML.TargetBlank',true);
        $obj = new HTMLPurifier($config);
    }
    return $obj->purify($val);
}

/**
 * @param $imgName
 * @param $dirName
 * @param array $thumb
 * @return array
 * @desc 上传图片
 */
function uploadOne($imgName,$dirName,$thumb = array())
{
    //上传logo
    if(isset($_FILES[$imgName]) && $_FILES[$imgName]['error'] == 0)
    {
        $rootPath = C('IMG_rootPath');
        $upload = new \Think\Upload(array(
            'rootPath'=>$rootPath,
        ));//实例化上传类
        $upload->maxSize = (int)C('IMG_maxsize')*1024*1024;//上传图片限制大小
        $upload->exts = C('IMG_exts');//设置附件上传类型
        $upload->savePath = $dirName.'/ ';//图片二级目录名称
        //上传文件
        $info  = $upload->upload();
        if(!$info)
        {
            return array(
              'ok' => 0,
              'error' => $upload->getError(),
            );
        }
        else
        {
            $ret['ok'] = 1;
            //拼出缩略图文件名
            $ret['images'][0] = $logoName =  $info[$imgName]['savepath'].$info[$imgName]['savename'];
            if($thumb)
            {
                foreach ($thumb as $k =>$v)
                {
                    $ret['images'][$k+1] = $smLogoName = $info[$imgName]['savepath'].'thumb_'.$k.'_'.$info[$imgName]['savename'];
                    //生成缩略图
                    $image = new \Think\Image();
                    //打开要处理的图片
                    $image->open($rootPath.$logoName);
                    $image->thumb($v[0],$v[1])->save($rootPath.$ret['images'][$k+1]);
                }
            }
            return $ret;
        }
    }
}

function showImage($url,$width='',$height='')
{
    $url = '/Public/Uploads/'.$url;
    if($width)
    {
        $width = "width='$width'";
    }
    if($height)
    {
        $height = "height='$height'";
    }
    echo "<img url='$url' $width $height />";
}

function deleteImage($images)
{
    //先取出图片所在目录
    $rp = C('IMG_rootPath');
    foreach($images as $v)
    {
        unlink($rp.$v);
    }
}