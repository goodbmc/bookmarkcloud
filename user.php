<?php
if (!defined('ROOT')) {
    require_once './path_root.php';
}
require_once ROOT . '/app/server/system/functions.php';

session_start();
// 数据库配置和连接代码...
$dbHandler = new DatabaseHandler($database);

$urlPath = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$filePath = $_SERVER['DOCUMENT_ROOT'] . $requestPath;
$urlPath = preg_replace('/^\/' . ROOT_DIR .'(\/index\.php)?\//', '', $urlPath);

if (empty($urlPath) || trim($urlPath, '/') === '' || trim($urlPath, '/') === 'index.php') {
        // 根路径处理
        handleRootPath();
    } else {
    // 检查文件是否存在并且是文件而不是目录
    if (file_exists($filePath) && is_file($filePath)) {
    // 检查文件是否可读
    if (is_readable($filePath)) {
// 不做处理
    } else {
        // 文件存在但不可读
        http_response_code(403); // 禁止访问
        echo "Forbidden";
        exit;
    }
} else {
           	 // 用户路径处理
             handleUserPath($urlPath);
        }
    }


function handleRootPath() {
    global $dbHandler;
    if (!empty($_SESSION['user_id']) && !empty($_SESSION['username'])) {
        // 已经登录，重定向到用户页面
        // 构造重定向URL
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            $redirectPath = '/' . ltrim((ROOT_DIR !== '' ? ROOT_DIR . '/' : ''), '/');
            $redirectUrl = $protocol . $host . $redirectPath . $_SESSION['username'];
            header('Location: ' . $redirectUrl);
            exit();
    } else {
        // 未登录，尝试获取最小ID用户
        $userInfo = $dbHandler->minUser();
        if ($userInfo) {
            // 设置会话并重定向
            $_SESSION['user_id'] = $userInfo['id'];
            $_SESSION['username'] = $userInfo['username'];
            // 构造重定向URL
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            $redirectPath = '/' . ltrim((ROOT_DIR !== '' ? ROOT_DIR . '/' : ''), '/');
            $redirectUrl = $protocol . $host . $redirectPath . $userInfo['username'];
            header('Location: ' . $redirectUrl);
            exit();
        } else {
            // 管理员未找到，重新注册
            header('Location:install.php');
            exit;
        }
    }
}

function handleUserPath($urlPath) {
    global $dbHandler;
    $pathSegments = explode('/', trim($urlPath, '/'));
    $username = end($pathSegments);
    $userId = $dbHandler->checkUser($username);
    if(!empty($userId) && !empty($_SESSION['username']) && $_SESSION['username'] === $username){
    	// 不做处理
    	} else{
    if ($userId) {
        // 更新会话信息（如果必要）
        if ($_SESSION['username'] !== $username) {
            $_SESSION['old_username'] = $_SESSION['username'];
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $userId;
            $_SESSION['loggedin'] = false; // 确保登录状态正确
        }
            // 构造重定向URL
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            $redirectPath = '/' . ltrim((ROOT_DIR !== '' ? ROOT_DIR . '/' : ''), '/');
            $redirectUrl = $protocol . $host . $redirectPath . $username;
            header('Location: ' . $redirectUrl);
            exit();
    } else {
        // 用户未找到
        echo "<h1>Error</h1>";
        echo "<p>User $username not found.</p>";
        exit;
    }
    }
}
//exit;
