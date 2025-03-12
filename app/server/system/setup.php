<?php
require_once 'functions.php';
session_start(); // 在使用$_SESSION之前务必加入这一行
$uid = $_POST['uid'];
$user_id = $_POST['uid'];
//var_dump($_SESSION['user_id']);
$username = $_POST['username'];
$password = $_POST['password'];
$newpassword = $_POST['newpassword'];
$email = $_POST['email'];
$catergory_open = $_POST['catergory_open']?? null;
$link_open = $_POST['link_open']?? null;
$all_open = $_POST['all_open']?? null;
$themes = $_POST['themes']?? 'default';

if ($catergory_open === null) {
    $catergory_open = 'off';
}

if ($link_open === null) {
    $link_open = 'off';
}

if ($all_open === null) {
    $all_open = 'off';
}

try {
    if (empty($username)) {
        throw new Exception('用户名不能为空！');
    }
    if (empty($password)) {
        throw new Exception('原密码不能为空！');
    }
    // 验证邮箱格式
    if (!empty($email)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('无效的邮箱地址！');
    }
    }
    if (!empty($newpassword)) {
    $result = validatePassword($newpassword);
    if (!$result['isValid']) {
        $errors = implode("\n", $result['errors']);
        throw new Exception("密码不满足条件: " . $errors);
    }
}
  // 检查 $_SESSION 是否已定义
if (isset($GLOBALS['_SESSION'])) {
    // 获取 user_id，如果不存在则设为 null
    $user_id_old = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
} else {
    $user_id_old = null;
}
// 输出调试信息
//echo "user_id_old: " . var_export($_SESSION, true) . "<br>";
//echo "user_id_old: " . var_export($user_id_old, true) . "<br>";
//echo "uid: " . var_export($uid, true) . "<br>";
   if ($user_id_old === null || $user_id_old != $uid) {
     throw new Exception('用户已变更，不能更新！');
   }

    $dbHandler = new DatabaseHandler($database);
    $setupusers = $dbHandler->updateUsers($user_id, $username, $password, $newpassword, $email, $catergory_open, $link_open, $all_open, $themes);

    if ($setupusers) {
        // 如果注册成功，则输出成功的JSON响应
        echo json_encode(['success' => true, 'message' => '更新成功！']);
    } else {
        // 如果注册失败，则抛出异常或输出错误信息
        throw new Exception('更新失败！');
    }

} catch (Exception $e) {
    // 捕获异常，并输出带有错误信息的JSON响应
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
