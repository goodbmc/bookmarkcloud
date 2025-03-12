<?php
require_once 'functions.php';
session_start();

// 文件上传相关变量初始化
$target_dir = "../../uploads/"; // 上传文件存放目录

$favicon = $_POST['favicon'] ?? null; // 已有favicon的网络地址
$favicon_image = $_FILES["fileToUpload"] ?? null; // 用户尝试上传的favicon图片

$user_id = $_POST['uid'];
$linkId = $_POST['id'];
$title = $_POST['title'];
$url = $_POST['url'];
$description = $_POST['description'];
$fid = $_POST['category'];
$ispublic = $_POST['ispublic'] ?? 'off';
$priority = $_POST['priority'] ?? 0;

try {
    if ($_SESSION['user_id'] === null || $_SESSION['user_id'] != $user_id) {
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
    $insertResult = $dbHandler->updateLink($linkId, $title, $url, $description, $favicon, $favicon_image, $fid, $ispublic, $priority);

    if (!$insertResult) {
        throw new Exception('更新网站信息失败！');
    }

    echo json_encode(['success' => true, 'message' => '网站信息及favicon已成功更新！']);

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
