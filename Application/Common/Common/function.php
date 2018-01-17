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