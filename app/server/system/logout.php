<?php
if(!defined('ROOT')) {
    require_once '../../../path_root.php';
}
session_start();
$savedUser = $_SESSION['username'] ?? null;
// 清除所有与用户相关的会话变量
$_SESSION = array();
// 销毁整个会话，并重置会话ID以防止会话固定攻击
session_unset();
session_destroy();
// 重定向用户回到登录页面或其他公共页面
header('Location: /' . ROOT_DIR);
exit;
?>
