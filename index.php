<?php
if(!defined('ROOT')) {
    require_once './path_root.php';
}
require_once __BMC_SYSTEM__ . 'functions.php';
// 数据库配置和连接代码...
$dbHandler = new DatabaseHandler($database);
// 检查用户名是否为空
        $theme = $dbHandler->getTheme();
        $theme_path = __BMC_THEMES__ . $theme;
        // 用户不同时，销毁当前用户的会话
    if(!empty($theme)){
        include 'app/themes/' . $theme . '/index.php';
        exit; // 退出脚本，防止后续输出
      }else {
      	echo "<h1>Error</h1>";
        echo "<p>themes not found.</p>";
      	}
?>
