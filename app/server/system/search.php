<?php
if(!defined('ROOT')) {
    require_once '../../../path_root.php';
}
require_once 'functions.php';
//session_start(); // 在使用$_SESSION之前务必加入这一行

$user_id = $_POST['uid'];
$keywords = $_POST['keywords'];

try {
    if (empty($user_id)) {
       throw new Exception('用户不能为空！');
        }
    if (empty($keywords)) {
            throw new Exception('关键词不能为空！');
        }
    if (isset($_SESSION['username']) && isset($_SESSION['old_username'])) {
                // 检查登录的用户名是否与当前用户名不同
                if ($_SESSION['username'] != $_SESSION['old_username'])
                {
                    throw new Exception('用户已更改！');
                    $_SESSION['old_username'] = $_SESSION['username'];
                }
        }
if(!empty($user_id) && !empty($keywords)) {
$_SESSION['keywords'] = $keywords;
echo json_encode(['success' => true, 'message' => '成功！']);
}
    // 其他逻辑
} catch (Exception $e) {
    // 处理异常
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
