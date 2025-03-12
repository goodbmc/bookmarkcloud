<?php
if (!defined('ROOT')) {
    require_once './path_root.php';
}
require_once __BMC_SYSTEM__ . 'functions.php';

$dbHandler = new DatabaseHandler($database);
$userInfo = $dbHandler->minUser();

// 检查是否已有管理员账号存在
if ($userInfo) {
    echo "<h1>Error</h1>
    <p>无需安装，管理员账号已存在！</p>
    <a href=\"index.php\">返回首页</a>";
    // 增加安全性，防止XSS攻击
    // 注意这里的输出已经是静态文本，但如果包含动态数据，应该使用 htmlspecialchars() 进行转义
    exit;
}

$errors = [];
$successMessage = '';
$username = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 清理并获取表单数据
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    // 验证用户名、密码和邮箱格式
    if (empty($username) || empty($password) || empty($email)) {
        $errors[] = '所有字段都是必填的。';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = '无效的邮箱地址！';
    }
    $validatePassword = validatePassword($password);
    if (!$validatePassword['isValid']) {
        $errors = array_merge($errors, $validatePassword['errors']);
    }

    // 如果没有错误，则尝试注册新用户
    if (empty($errors)) {
        $pin = generateRandomString(6);
        $is_admin = 1;
        $register = $dbHandler->registerUser($username, $password, $email, $pin, $is_admin);

        if ($register['success'] === true) {
            $uid = $dbHandler->checkUser($username);
            if ($uid !== false) {
                $dbHandler->insertCategory($uid, '默认分类', 'on', 0);
                $fid = $dbHandler->getMinCategoryByUid($uid);
                $dbHandler->insertLink($uid, 'bookmark书签云', 'http://www.bookmark.cloud', '致力于极简云书签管理，网址导航！','http://www.bookmark.cloud/favicon.icon','default.png', $fid, 'on', 0);
                $_SESSION['loggedin'] = false;
                $_SESSION['loggedincurrent'] = $_SESSION['loggedin'];
                $successMessage = "注册成功！";
            } else {
                $errors[] = '初始化失败！';
            }
        } else {
            $errors[] = isset($register['error']) ? $register['error'] : '未知错误！';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>安装应用</title>
     <style>
        /* 基本重置 */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .error-messages {
            color: red;
            margin-bottom: 20px;
        }
        .popupForm {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,.2);
            width: 100%;
            max-width: 400px;
            text-align: left; /* 确保内部元素左对齐 */
        }

        .popupForm label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .popupForm input[type="text"],
        .popupForm input[type="password"],
        .popupForm input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .button-container {
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .submitBtn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #129FEA;
            color: white;
        }

        .notice {
            margin: 0;
            font-size: 0.9em;
            color: #F9342E;
            flex-grow: 1; /* 让提示信息占据剩余空间 */
            margin-right: 10px; /* 给提示文本右侧留出一些空间 */
        }
.backIndex {
	text-align: center;
  text-decoration: none; /* 去掉下划线 */
  color: #fff; /* 设置文字颜色，可选 */
  background-color: #129FEA; /* 背景颜色，可选 */
  padding: 10px 20px; /* 内边距，使它看起来更像按钮，可选 */
  border-radius: 5px; /* 圆角边框，可选 */
  display: inline-block; /* 让元素可以设置宽高以及使用margin和padding */
}

.backIndex:hover {
  opacity: 0.8; /* 降低透明度 */
}

/* 右对齐 */
.backIndex {
  margin-left: auto; /* 结合display: inline-block; 使用，可以让元素尽可能靠右 */
  display: block; /* 改变为块级元素以应用margin-left:auto实现右对齐 */
}
    </style>
</head>
<body>

<div id="registerPopupForm" class="popupForm">
    <?php if (!empty($successMessage)): ?>
        <div class="notice success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if (empty($successMessage)): ?>
    <form id="registerForm" method="post">
        <label for="username">用户名：</label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" required>
        <br>
        <label for="password">密码：</label>
        <input type="password" name="password" id="password" required>
        <br>
        <label for="email">邮箱：</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
        <div class='button-container'>
            <p class="notice">该账号将作为管理员账号，谨慎填写！</p>
            <button type="submit" id="submitregisterBtn" class="submitBtn">提交</button>
        </div>
    </form>
    <?php endif; ?>
    <?php if (!empty($successMessage)): ?>
        <div>
            <p>用户名: <?php echo htmlspecialchars($username); ?></p>
            <p>邮箱: <?php echo htmlspecialchars($email); ?></p>
            <p>PIN码: <?php echo htmlspecialchars($pin); ?>，用于忘记密码，妥善保管！</p>
            <br>
            <a href="index.php" class="backIndex">返回首页</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
