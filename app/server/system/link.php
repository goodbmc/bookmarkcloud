<?php
require_once 'functions.php';
session_start(); // 在使用$_SESSION之前务必加入这一行

// 文件上传相关变量初始化
$target_dir = "../../uploads/"; // 上传文件存放目录

$favicon = $_POST['favicon'] ?? null; // 已有favicon的网络地址
$favicon_image = $_FILES["fileToUpload"] ?? null; // 用户尝试上传的favicon图片


$uid = $_POST['uid'];
$title = $_POST['title'];
$url = $_POST['url'];
$description = $_POST['description'];
$fid = $_POST['category'];
$ispublic = $_POST['ispublic']?? null;
$priority = $_POST['priority'];
//var_dump($user_id_old);
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
   if ($user_id_old === null || $user_id_old != $uid) {
     throw new Exception('用户已变更，不能更新！');
   }

    if (empty($title) || empty($url)) {
       throw new Exception('网站标题和网址不能为空！');
    }
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new Exception('网址格式不正确！');
    }

    if ($favicon_image && $favicon_image['size'] > 0) { // 如果用户上传了favicon
        $imageFileType = strtolower(pathinfo(basename($favicon_image["name"]), PATHINFO_EXTENSION));
        $sanitizedUrl = preg_replace('/[^A-Za-z0-9]/', '', $_POST['url']);
        $new_filename = $_SESSION['user_id'] . $sanitizedUrl . '.' . $imageFileType;
        $target_file = $target_dir . $new_filename;

        // 检查上传文件是否为图像
        $check = getimagesize($favicon_image["tmp_name"]);
        if ($check !== false) {
            list($width, $height) = getimagesize($favicon_image["tmp_name"]);
            if ($width > 200 || $height > 200) {
                throw new Exception("抱歉，您的图片太大了，请上传200x200或更小的图片。");
            }

            // 使用 finfo 检查文件类型
      $finfo = new finfo(FILEINFO_MIME_TYPE);
      $mimeType = $finfo->file($favicon_image["tmp_name"]);
      var_dump($mimeType);
      $validImageTypes = ['image/x-icon', 'image/vnd.microsoft.icon', 'image/png', 'image/jpeg', 'image/gif'];
      if (!in_array($mimeType, $validImageTypes)) {
          throw new Exception("文件不是一个有效的图片类型。");
      }
            if (move_uploaded_file($favicon_image["tmp_name"], $target_file)) {
                $favicon_image = $new_filename;
            } else {
                throw new Exception("对不起，上传文件时发生错误。");
            }
        } else {
            throw new Exception("文件不是一个有效的图片。");
        }
    } elseif ($favicon) { // 如果没有上传新favicon，则从网络地址下载
        $faviconFileName = basename(parse_url($favicon, PHP_URL_PATH));
        $imageFileType = pathinfo($faviconFileName, PATHINFO_EXTENSION);
        $sanitizedUrl = preg_replace('/[^A-Za-z0-9]/', '', $_POST['url']);
        $new_filename = $_SESSION['user_id'] . $sanitizedUrl . '.' . $imageFileType;
        $target_file = $target_dir . $new_filename;

        // 使用 cURL 下载 favicon
        $ch = curl_init($favicon);
        $fp = fopen($target_file, 'wb');
        if (!$fp) {
            throw new Exception("无法打开目标文件进行写入: $target_file");
        }
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 如果需要SSL验证，请移除或配置正确的CA证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 设置超时时间
        curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception("下载文件时发生错误: " . curl_error($ch));
        }
        curl_close($ch);
        fclose($fp);

        // 确认下载的文件是有效的图片
        $check = getimagesize($target_file);
        if ($check === false) {
            $validImageTypes = ['image/x-icon', 'image/vnd.microsoft.icon', 'image/png', 'image/jpeg', 'image/gif'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($target_file);
            // 调试输出
            var_dump($mimeType);
            //echo "Detected MIME Type: " . $mimeType;
            if (!in_array($mimeType, $validImageTypes)) {
                throw new Exception("下载的文件不是一个有效的图片。");
            }
        }

        $favicon_image = $new_filename;
    } else {
      $favicon_image = '';
      //  throw new Exception("没有提供favicon信息。");
    }
    $dbHandler = new DatabaseHandler($database);
    $insertResult = $dbHandler->insertLink($uid,$title, $url, $description, $favicon, $favicon_image, $fid, $ispublic, $priority);

    if (!$insertResult) {
        throw new Exception('插入网站信息失败！');
    }

    echo json_encode(['success' => true, 'message' => '网站信息已成功提交！']);

} catch (Exception $e) { error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
