<?php

function fetchPageInfo($url) {
    // 验证并清理URL
    $url = filter_var(trim($url), FILTER_VALIDATE_URL);
    if (!$url) {
    //http_response_code(400); // 设置HTTP状态码为400
    header('Content-Type: application/json');
    echo json_encode(['error' => '无效的URL！']);
    //return; // 结束当前函数
    exit();//结束整个函数的运行
    }

    // 初始化cURL会话
    $ch = curl_init();
       // $info = curl_getinfo($ch);
//print_r($info); // 查看所有信息
    // 设置cURL选项
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟踪重定向
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; PHP bot)'); // 模拟用户代理
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 如果需要SSL验证，请移除或配置正确的CA证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);// 设置连接超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);// 设置请求超时时间

    // 执行cURL会话并获取结果
    $html = curl_exec($ch);
   // print_r($html);
    // 检查是否有错误发生
    
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
        return [];
    }
    
    // 关闭cURL会话
    curl_close($ch);

    // 使用DOMDocument加载HTML
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    // 创建XPath对象以便更方便地查找元素
    $xpath = new DOMXPath($dom);

    // 初始化返回数组
    $meta = [];
   // 获取favicon
    $faviconNodes = $xpath->query('//link[@rel="icon" or @rel="shortcut icon"]/@href');
    if ($faviconNodes->length > 0) {
        foreach ($faviconNodes as $node) {
            // 确保favicon URL是绝对路径
            $faviconUrl = makeAbsoluteUrl($node->nodeValue, $url);
            if (filter_var($faviconUrl, FILTER_VALIDATE_URL)) {
                $meta['favicon'] = $faviconUrl;
               // $meta['faviconBase64'] = getImageBase64($faviconUrl);
                break; // 找到第一个有效的favicon后退出循环
            }
        }
    }

    // 如果没有找到favicon，尝试使用默认路径
    if (!isset($meta['favicon'])) {
        $defaultFavicon = rtrim($url, '/') . '/favicon.ico';
        if (checkUrlExists($defaultFavicon)) {
            $meta['favicon'] = $defaultFavicon;
            // base64 占存储 不用了
           // $meta['faviconBase64'] = getImageBase64($defaultFavicon);
        } else {
            $meta['favicon'] = null;
          //  $meta['faviconBase64'] = null;
            
        }
    }
    //var_dump($defaultFavicon);
    return $meta;
}

// 检查给定的URL是否存在
function checkUrlExists($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true); // 不需要body内容
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟踪重定向
    curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ($responseCode == 200);
}

// 将相对URL转换为绝对URL
function makeAbsoluteUrl($relativeUrl, $baseUrl) {
    // 解析基本URL
    $base = parse_url($baseUrl);
    
    // 如果相对URL已经是完整的URL，则直接返回
    if (filter_var($relativeUrl, FILTER_VALIDATE_URL)) {
        return $relativeUrl;
    }

    // 处理协议相对URL（以'//'开头）
    if (strpos($relativeUrl, '//') === 0) { 
        return $base['scheme'] . ':' . $relativeUrl;
    }

    // 确保有$path值，默认为'/'
    $base['path'] = isset($base['path']) ? $base['path'] : '/';

    // 如果相对URL是绝对路径（以'/'开头），则直接连接到主机后面
    if (substr($relativeUrl, 0, 1) === '/') { 
        return $base['scheme'] . '://' . $base['host'] . $relativeUrl;
    }

    // 对于其他情况（即相对路径），构建绝对路径
    // 首先获取基本URL的路径部分，并去掉末尾的文件名，保留目录部分
    $path = dirname($base['path']);
    // 返回由基本URL的协议、主机名和路径组成的绝对URL加上相对URL
    return $base['scheme'] . '://' . $base['host'] . ($path === '/' ? '' : $path . '/') . $relativeUrl;
}

function getImageBase64($imageUrl) {
    // 初始化cURL会话
    $ch = curl_init();

    // 设置cURL选项
    curl_setopt($ch, CURLOPT_URL, $imageUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回结果而不是直接输出
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟踪重定向
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; PHP bot)'); // 模拟用户代理
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 如果需要SSL验证，请移除或配置正确的CA证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // 执行cURL会话并获取图片数据
    $imageData = curl_exec($ch);

    if (curl_errno($ch)) {
    	// 不用报错，附加功能
      //  echo 'Curl error: ' . curl_error($ch);
        return null;
    }

    // 关闭cURL会话
    curl_close($ch);

    // 将图片数据转换为base64编码
    $base64Image = base64_encode($imageData);

    // 返回base64编码的数据
    return $base64Image;
}

// 如果是http://没有www.的增加上 如果是https://去掉www. 局域网不做处理
function normalize_url($url) {
    // 解析URL
    $parsedUrl = parse_url($url);
    
    // 确保scheme和host存在
    if (!isset($parsedUrl['scheme']) || !isset($parsedUrl['host'])) {
        return false; // 返回false表示解析失败
    }

    $scheme = strtolower($parsedUrl['scheme']);
    $host = $parsedUrl['host'];
    $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
    $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
    $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';

    // 检查是否为 localhost 或 IP 地址
    if (filter_var($host, FILTER_VALIDATE_IP) !== false || $host === 'localhost') {
        // 如果是 IP 地址或 localhost，则不修改 host
        $normalizedHost = $host;
    } else {
        // 根据协议类型处理 "www."
        if ($scheme === 'http') {
            // 如果是HTTP且没有 "www."，则添加 "www."
            if (strpos($host, 'www.') !== 0) {
                $normalizedHost = 'www.' . $host;
            } else {
                $normalizedHost = $host;
            }
        } elseif ($scheme === 'https') {
            // 如果是HTTPS，则移除 "www."
            $normalizedHost = preg_replace('/^www\./i', '', $host);
        }
    }

    // 重新构建URL
    $normalizedUrl = $scheme . '://' . $normalizedHost . $path . $query . $fragment;

    return $normalizedUrl;
}



// 检查请求方法和数据存在性
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    // 调用函数获取页面信息
    $pageInfo = fetchPageInfo(normalize_url($_POST['url']));

    // 设置响应头为JSON格式
    header('Content-Type: application/json');
    // 如果没有找到标题，则认为是一个错误
    if (!isset($pageInfo['error'])) {
    	
    	if($pageInfo['favicon'] !== null){
    	  // 返回获取到的信息
        echo json_encode([
            'favicon' => $pageInfo['favicon'],
            //'faviconBase64' => $pageInfo['faviconBase64']
            ]);
            } else {
            echo json_encode(['favicon' => null]);
          }
    } else {
        http_response_code(400); // 坏请求
        //echo json_encode(['error' => $pageInfo['error']]);
        echo json_encode(['favicon' => null]);
    }
} else {
    // 输出错误信息作为JSON响应
    header('Content-Type: application/json');
    http_response_code(400); // 坏请求
    echo json_encode(['favicon' => null]);
}
?>