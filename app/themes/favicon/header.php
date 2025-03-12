<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-dns-prefetch-control" content="on">
    <link rel="dns-prefetch" href="//cdn.bootcss.com">
    <link rel="dns-prefetch" href="//secure.gravatar.com">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-transform"/>
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/images/favicon.png">
    <link rel="apple-touch-icon" href="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/images/apple-touch-icon.png">
    <title>bookmarkcloud--导航</title>
    <meta name="keywords" content="">
    <meta name="description" content="好用的网址导航APP">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/static/style.min.css?20240216">
    <script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/static/headroom.min.js"></script>
    <script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/static/customalert.js"></script>
    <!-- <script src="./static/tooltip.js" ></script> -->
    <!--[if lt IE 9]>
        <script src="//cdn.bootcss.com/html5shiv/r29/html5.min.js"></script>
        <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<!-- 主题颜色 -->
<body class="bg-white">
    <!--[if lt IE 8]>
        <div class="browsehappy" role="dialog">
            当前网页<strong>不支持</strong>你正在使用的浏览器。为了正常访问，请<a href="http://browsehappy.com/" target="_blank">升级你的浏览器</a>。
        </div>
    <![endif]-->
    <!-- 自定义弹窗提醒 -->
<div id="customOverlay" class="overlayAlter"></div>
<div id="customAlert" class="popupAlter">
    <p id="alertMessage"></p>
    <div class='button-container'>
    <button id="alertOkButton" class="submitBtn">确定</button>
    </div>
</div>

    <header id="header" class="header bg-white">
        <div class="navbar-container">
            <a href="" class="navbar-logo">
                <img src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/images/logo.png" alt="BOOKMARKCLOUD">
            </a>

            <div class="navbar-menu">
                <a href="">导航</a>
                <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <a href="#" id="openFormLink">添加网址</a>
                <a href="#" id="addCategory">添加分类</a>
                <div class="dropdown">
                <a href="#">用户中心</a>
                <div class="dropdown-content">
                	<a href="#" id="openFormsetup">设置</a>
                  <a href="app/server/system/logout.php">退出</a>
                </div>
                </div>
                <?php else: ?>
                <a href="#" id="login">登录</a>
                <a href="#" id="register">注册</a>
                <?php endif; ?>
            </div>
        <?php
        if (isset($_SESSION['user_id'])&& $_SESSION['user_id'] != null): // Note the colon at the end to denote the start of the block
          ?>
        <a href="#" id="search"> <span class="icon-search"></span></a>
        <?php endif; ?>
            <div class="navbar-mobile-menu" onclick="">
                <span class="icon-menu cross"><span class="middle"></span></span>
                <ul>
                    <li><a href="">导航</a></li>
                    <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <li><a href="#" id="openFormLinkmobile">添加网址</a></li>
                    <li><a href="#" id="addCategorymobile">添加分类</a></li>
                    <li class="dropdown-mobile">
                    <a href="#">用户中心</a>
                    <div class="dropdown-mobile-content">
                	   <a href="#" id="openFormsetupmobile">设置</a>
                     <a href="app/server/system/logout.php">退出</a>
                    </div>
                    </li>

                    <?php else: ?>
                    <li><a href="#" id="loginmobile">登录</a></li>
                    <li><a href="#" id="registermobile">注册</a></li>
                    <?php endif; ?>
                </ul>
                </div>
            </div>

<script>
    var myHeader = new Headroom(document.querySelector("#header"), {
    tolerance: 5,
    offset: 205,
    classes: {
        initial: "animated",
        pinned: "slideDown",
        unpinned: "slideUp"
    }
});
myHeader.init();
</script>
    </header>
