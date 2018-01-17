<?php
return array(
	//'配置项'=>'配置值'
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'rc_shop',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  '',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'rc_',    // 数据库表前缀
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8

    /**********图片相关配置***********/
    'IMG_maxSize' => '3M',
    'IMG_exts' => array('jpg','pjpeg','bmp','gif','png','jpeg'),
    'IMG_rootPath' =>'./Public/Uploads/',
    /*************** 修改I函数底层过滤时使用的函数 **********/
    'DEFAULT_FILTER' => 'trim,removeXSS',
    /*********** MD5复杂化的KEY ********/
    'MD5_KEY' => '3D0FFE5291E42782F694FACFF526C22C',
);