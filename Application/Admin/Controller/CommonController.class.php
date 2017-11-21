<?php
namespace Admin\Controller;
use Think\Controller;

class CommonController extends Controller{
    /**
     * 生成guid
     * @return string
     */
function getGuid(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = ''// "{"
            .substr($charid, 0, 8)
            .substr($charid, 8, 4)
            .substr($charid,12, 4)
            .substr($charid,16, 4)
            .substr($charid,20,12)
        ;// "}"
        return $uuid;
    }
}

function reSetKey(){
    $key = $this->getGuid();
    dump($key);
    F('MD5_KEY',$key,'.\Application\Common\Conf\config.php');
    dump(F('MD5_KEY'));
    dump(CONF_PATH);
}

}