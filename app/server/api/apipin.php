<?php
//获取PIN码：http://localhost/api.php?action=getPin
//更新PIN码：http://localhost/api.php?action=updatePin
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


// 检查请求方法，这里只处理GET请求
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    exit;
}

// 获取动作参数
$action = isset($_GET['action']) ? $_GET['action'] : null;

if (empty($action)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Action parameter is required']);
    exit;
}

// 根据'action'参数调用不同的函数
switch ($action) {
    case 'getPin':
        handleGetPin();
        break;
    case 'updatePin':
        handleUpdatePin();
        break;
    default:
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Unknown action']);
        break;
}

function handleGetPin() {
	global $dbHandler;
	$getPin = $dbHandler->getPin();
	if ($getPin) {
    header('Content-Type: application/json');
    echo json_encode($getPin);
} else {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Pin not found']);
}
 }

function handleUpdatePin() {
	global $dbHandler;
	$uid = htmlspecialchars((int) $_SESSION['user_id']);
	$pin = generateRandomString(6);
	$updatePin = $dbHandler->updatePin($uid,$pin);
	if ($updatePin) {
   handleGetPin();
	}
}
?>
