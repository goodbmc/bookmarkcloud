<?php
require_once 'functions.php';
session_start(); // 在使用$_SESSION之前务必加入这一行
$uid = $_POST['uid'];
$category = $_POST['category'];
$ispublic = $_POST['ispublic']?? null;
$priority = $_POST['priority'];
// 如果 $isPublic 是 null，则赋值为 'off'
if ($ispublic === null) {
    $ispublic = 'off';
}
if ($priority === null || $priority === '') {
    $priority = 0;
}
try {
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
    if (empty($category)) {
        throw new Exception('分类名称不能为空！');
    }

    $dbHandler = new DatabaseHandler($database);
    $insertResult = $dbHandler->insertCategory($uid, $category, $ispublic, $priority);

    if (!$insertResult) {
        throw new Exception('插入分类失败！');
    }

    echo json_encode(['success' => true, 'message' => '网站信息已成功提交！']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
