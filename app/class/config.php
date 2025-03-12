<?php
// 引入Medoo类库
require 'Medoo.php';
use Medoo\Medoo;

// 定义数据库配置
define('DB_TYPE', 'sqlite');
// app/class/config.php
define('DB_FILE', __DIR__ . '/../data/bookmarkcloud.db3');

// 创建数据库连接实例
$database = new Medoo([
    'type' => DB_TYPE,
    'file' => DB_FILE,
    'database' => DB_FILE,
    'return_pdo' => true, // 设置为true以便能够获取PDO实例
]);

//define('__BOOKMARKCLOUDDIR__', str_replace('\\', '/',__DIR__));
//var_dump(__BOOKMARKCLOUDDIR__);

// 注意：出于安全性考虑，用户名和密码不应在此处硬编码，这里仅作为示例
// 真实情况下应从环境变量、密钥管理服务或更安全的方式获取
// define('USER', '{username}');
// define('PASSWORD', '{password}');
