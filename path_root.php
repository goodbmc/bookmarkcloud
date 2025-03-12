<?php
    // 全局网站根目录
if (!defined('ROOT')) {
    // 使用正斜杠代替反斜杠，解决不同系统移植问题
    define('ROOT', rtrim(str_replace('\\', '/', realpath(__DIR__)), '/') . '/');
}

// 获取DOCUMENT_ROOT，并进行标准化处理
$documentRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/') . '/';

if (!defined('ROOT_DIR')) {
    if (ROOT === $documentRoot) {
        // 如果ROOT等于DOCUMENT_ROOT，则认为是在根目录下
        define('ROOT_DIR', '');
    } else {
        // 否则，获取当前目录相对于根目录的名字
        define('ROOT_DIR', basename(ROOT));
    }
}
// var_dump(ROOT);
// var_dump(ROOT_DIR);
//CLASS路径
define('__BMC_CLASS__','app/class/');
//var_dump(__BMC_CLASS__);
//数据路径
define('__BMC_DATA__','app/data/');
//var_dump(__BMC_DATA__);
//API路径
define('__BMC_API__','app/server/api/');
//var_dump(__BMC_API__);
//系统路径
define('__BMC_SYSTEM__','app/server/system/');
//var_dump(__BMC_SYSTEM__);
//主题路径
define('__BMC_THEMES__','app/themes/');
//var_dump(__BMC_THEMES__);


/*
各级引用示例：
if(!defined('ROOT')) {
    require_once './path_root.php';
}

一级：require_once './path_root.php';
二级：require_once '../path_root.php';
三级：require_once '../../path_root.php';
四级：require_once '../../../path_root.php'

*/
require_once 'user.php'
?>
