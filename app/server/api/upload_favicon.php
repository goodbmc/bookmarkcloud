<?php
$target_dir = "../uploads/"; // 上传文件存放目录

// 获取文件扩展名
$imageFileType = strtolower(pathinfo(basename($_FILES["fileToUpload"]["name"]), PATHINFO_EXTENSION));

// 创建唯一的文件名
$new_filename = uniqid('img_', true) . '.' . $imageFileType;
$target_file = $target_dir . $new_filename;

$uploadOk = 1;

// 检查是否有文件被上传
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;

        // 确保图片尺寸不超过100x100
        list($width, $height) = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($width > 200 || $height > 200) {
            echo "抱歉，您的图片太大了，请上传200x200或更小的图片。";
            $uploadOk = 0;
        }
    } else {
        echo "文件不是一个有效的图片。";
        $uploadOk = 0;
    }
}

// 检查$uploadOk是否被设置为0，如果是，则文件不应上传
if ($uploadOk == 0) {
    echo "对不起，你的文件没有上传。";
// 如果一切正常，尝试上传文件
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "文件 ". htmlspecialchars($new_filename). " 已经上传成功。";
    } else {
        echo "对不起，上传文件时发生错误。";
    }
}
?>