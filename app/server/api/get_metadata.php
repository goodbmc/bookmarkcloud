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
    
    // 设置cURL选项
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟踪重定向
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; PHP bot)'); // 模拟用户代理
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 如果需要SSL验证，请移除或配置正确的CA证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);// 设置连接超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);// 设置请求超时时间

    // 执行cURL会话并获取结果
    $html = curl_exec($ch);
    
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

    // 获取<title>标签的内容
    $titleNode = $dom->getElementsByTagName('title')->item(0);
    if ($titleNode) {
        $meta['title'] = trim($titleNode->textContent);
    } else {
        $meta['title'] = null;
    }

    // 查找<meta>标签中的description属性
    $descriptionNodes = $xpath->query('//meta[@name="description" or @property="og:description"]/@content');
    if ($descriptionNodes->length > 0) {
        $meta['description'] = trim($descriptionNodes->item(0)->nodeValue);
    } else {
        $meta['description'] = null;
    }

    return $meta;
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
    	
    	if($pageInfo['title'] !== null){
    	  // 返回获取到的信息
        echo json_encode([
            'title' => $pageInfo['title'] ?? '无法获得标题，请手动输入！',
            'description' => $pageInfo['description'] ?? '无法获得描述，请手动输入！'
            ]);
            } else {
            echo json_encode(['error' => '无法获得标题，请手动输入！']);
          }
    } else {
        http_response_code(400); // 坏请求
        //echo json_encode(['error' => $pageInfo['error']]);
        echo json_encode(['error' => '无法获得标题，请手动输入！']);
    }
} else {
    // 输出错误信息作为JSON响应
    header('Content-Type: application/json');
    http_response_code(400); // 坏请求
    echo json_encode(['error' => '没有提交数据！']);
}
?>