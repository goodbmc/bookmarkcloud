<?php
require_once 'functions.php';
session_start(); // 在使用$_SESSION之前务必加入这一行

$id = $_POST['id'];
if(isset($_POST['newcategory'])){
$fid = $_POST['newcategory'];
}
$user_id = $_POST['uid']; // 用户不同时不能提交，这个等等改下
$delete_links = $_POST['delete_links']?? null;
// 如果 $isPublic 是 null，则赋值为 'off'
if ($delete_links === null) {
try {
  // 检查 $_SESSION 是否已定义
if (isset($GLOBALS['_SESSION'])) {
    // 获取 user_id，如果不存在则设为 null
    $user_id_old = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
} else {
    $user_id_old = null;
}
  if ($user_id_old === null || $user_id_old != $user_id) {
     throw new Exception('用户已变更，不能更新！');
   }
    if (empty($fid)) {
        throw new Exception('分类名称不能为空！');
    }

    $dbHandler = new DatabaseHandler($database);
    $insertResult = $dbHandler->deleteCategory($id, $fid);

    if (!$insertResult) {
        throw new Exception('删除分类失败！');
    }

    echo json_encode(['success' => true, 'message' => '网站信息已成功提交！']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

}

if ($delete_links === 'on') {
try {
    $dbHandler = new DatabaseHandler($database);
    $insertResult = $dbHandler->deleteCategoryWithLinks($id);

    if (!$insertResult) {
        throw new Exception('删除分类失败！');
    }

    echo json_encode(['success' => true, 'message' => '网站信息已成功提交！']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

}
