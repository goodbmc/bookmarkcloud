<?php
require_once 'functions.php';

$pin = $_POST['pin'];
$password = $_POST['password'];
$email = $_POST['email'];
$newpin = generateRandomString(6);
try {
   if (empty($email)) {
      throw new Exception('邮箱不能为空！');
   }
    if (empty($pin)) {
        throw new Exception('PIN码不能为空！');
    }
    if (empty($password)) {
        throw new Exception('密码不能为空！');
    }

    $validatePassword = validatePassword($password);
// 检查验证结果
if (!empty($validatePassword) && $validatePassword['isValid'] === false) {
    // 将错误信息数组转换为字符串
    $errorMessage = is_array($validatePassword['errors']) ? implode('&', $validatePassword['errors']) : $validatePassword['errors'];
    // 抛出异常，包含具体的错误信息
    throw new Exception($errorMessage);
}

    // 验证邮箱格式
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('无效的邮箱地址！');
    }
    $dbHandler = new DatabaseHandler($database);
    $resetPassWord = $dbHandler->resetPassWord($pin, $password, $email, $newpin);
    if (isset($resetPassWord['success'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'message' => '密码重置成功！']);
  } elseif (isset($resetPassWord['error'])) {
        // 如果注册失败并且有错误信息，则抛出异常或输出错误信息
        echo json_encode(['error' => $resetPassWord['error']]);
    } else {
      // 如果既没有 'success' 也没有 'error'，则返回一个通用错误
        echo json_encode(['error' => '未知错误,请联系管理员处理！']);
        }
} catch (Exception $e) {
    // 捕获异常，并输出带有错误信息的JSON响应
    echo json_encode(['error' => $e->getMessage()]);
}
