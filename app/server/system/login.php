<?php
require_once 'functions.php';

$username = $_POST['username'];
$password = $_POST['password'];

try {
    if (empty($username)) {
        throw new Exception('用户名不能为空！');
    }
    if (empty($password)) {
        throw new Exception('密码不能为空！');
    }
    $dbHandler = new DatabaseHandler($database);
    $login = $dbHandler->loginUser($username, $password);

    if ($login) {
        // 如果注册成功，则输出成功的JSON响应
        echo json_encode(['success' => true, 'message' => '登录成功！']);
    } else {
        // 如果注册失败，则抛出异常或输出错误信息
        throw new Exception('登录失败！');
    }

} catch (Exception $e) {
    // 捕获异常，并输出带有错误信息的JSON响应
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
