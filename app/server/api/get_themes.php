<?php
function getThemes() {
    $directory = 'app/themes';
    $result = array();
    $scan = scandir($directory);

    foreach ($scan as $value) {
        if (!in_array($value, array(".", ".."))) {
            $fullPath = $directory . DIRECTORY_SEPARATOR . $value;
            if (is_dir($fullPath)) {
                $result[] = $value;
            }
        }
    }

    return $result;
}

// 调用函数并获取结果
$result = getThemes();

// 如果是通过 HTTP 请求访问，则输出 JSON 数据
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    //header('Content-Type: application/json');
    return json_encode($result);
} else {
    // 如果是直接在脚本中调用，则直接输出结果
    return $result;
}
?>
