<?php
require_once 'functions.php';

$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];
$pin = generateRandomString(6);
try {
    if (empty($username)) {
        throw new Exception('用户名不能为空！');
    }
    if (empty($password)) {
        throw new Exception('密码不能为空！');
    }
    if (empty($email)) {
        throw new Exception('邮箱不能为空！');
    }
    // 验证邮箱格式
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('无效的邮箱地址！');
    }

    $validatePassword = validatePassword($password);
    // 检查验证结果
    if (!empty($validatePassword) && $validatePassword['isValid'] === false) {
        // 将错误信息数组转换为字符串
        $errorMessage = is_array($validatePassword['errors']) ? implode('&', $validatePassword['errors']) : $validatePassword['errors'];
        // 抛出异常，包含具体的错误信息
        throw new Exception($errorMessage);
    }
    $dbHandler = new DatabaseHandler($database);
    $userId = $dbHandler->minUser();
    if(empty($userId)){
      $is_admin = 1;
    } else{
      $is_admin = 0;
    }
    $register = $dbHandler->registerUser($username, $password, $email, $pin, $is_admin);
    //var_dump($register);
    if (isset($register['success']) && is_bool($register['success']) && $register['success']) {
     //var_dump($register);
      // 获取用户的UID
     $uid = $dbHandler->checkUser($username);
      // 检查 $uid 是否为 false（表示用户不存在）
     if ($uid === false) {
            throw new Exception('初始化失败！');
        }
      //  var_dump($uid); 发布时注释掉，不然影响js获取json
     // 插入默认分类
     $insertCategory = $dbHandler->insertCategory($uid, '默认分类', 'on', 0);
     $fid = $dbHandler->getMinCategoryByUid($uid);
     $insertLink = $dbHandler->insertLink($uid, 'bookmark书签云', 'http://www.bookmark.cloud', '致力于极简云书签管理，网址导航！','http://www.bookmark.cloud/favicon.icon','default.png', $fid, 'on', 0);
     //var_dump($insertResult);
     // 防止自动登录
     $_SESSION['loggedin'] = false ;
     $_SESSION['loggedincurrent'] = $_SESSION['loggedin'];
    // 如果注册成功，则输出成功的JSON响应
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true, 'message' => '注册成功！']);

  } elseif (isset($register['error']) && is_string($register['error'])) {
        // 如果注册失败并且有错误信息，则抛出异常或输出错误信息
        throw new Exception($register['error']);
    } else {
        // 如果$register的格式不符合预期，这里可以添加额外的错误处理逻辑
        if (isset($register['success']) && is_bool($register['success']) && !$register['success']) {
            // 如果'success'键存在且为false，但没有具体的错误信息
            throw new Exception('注册失败，请稍后重试！');
        } else {
            throw new Exception('未知错误！');
        }
      }
} catch (Exception $e) {
    // 捕获异常，并输出带有错误信息的JSON响应
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
