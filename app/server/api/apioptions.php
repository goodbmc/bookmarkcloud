<?php
//session_start(); // 确保在使用session前已启动session
if(!defined('ROOT')) {
    require_once '../../../path_root.php';
}
require_once '../system/functions.php'; // 确保包含你的数据库操作类或函数
$dbHandler = new DatabaseHandler($database);

// 检查用户是否已经登录
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

// 确保getAllOptions是DatabaseHandler类的方法
if (!method_exists($dbHandler, 'getAllOptions')) {
    http_response_code(500);
    echo json_encode(['error' => 'Method getCategoryById does not exist in DatabaseHandler']);
    exit;
}

// 检查请求方法，这里只处理GET请求
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    exit;
}


// 正确调用方法，使用$dbHandler对象
$optionsDetails = $dbHandler->getAllOptions();

if ($optionsDetails) {
    header('Content-Type: application/json');
    echo json_encode($optionsDetails);
} else {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Category not found']);
}
?>
