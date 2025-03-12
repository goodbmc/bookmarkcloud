<?php
if(!defined('ROOT')) {
    require_once '../../../path_root.php';
};
require_once __BMC_SYSTEM__ . 'functions.php';
/*
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    // 直接访问
    die("对不起，您不能直接访问此页面。");
}*/
//require_once 'showmessage.php'; // 这个会引起菜单出问题,放在header后
require_once $theme_path . '/header.php';
require_once $theme_path . '/showmessage.php';
//var_dump($_SESSION);
// 检查用户是否已经登录
if (isset($_SESSION['username']) && isset($_SESSION['old_username'])) {
    // 检查登录的用户名是否与当前用户名不同
    if ($_SESSION['username'] != $_SESSION['old_username'])
    {
        showMessage("用户已更改！");
        $_SESSION['old_username'] = $_SESSION['username'];
    }
}
//showMessage("这是一个错误提示消息");
$user_id = $_SESSION['user_id'] ?? null;
//var_dump($_SESSION);
//var_dump($user_id);
// 检查 user_id 是否为空
if (empty($user_id)||$user_id===null) {
  // 如果没有用户名，可能是主页或其他页面
  // 你可以在这里添加其他逻辑
  $dbHandler = new DatabaseHandler($database);
  $userId = $dbHandler->minUser();
  //echo "<h1>User exists with minID: $userId</h1>";
  if ($userId) {
      $_SESSION['user_id'] = $userId;
      // 启动输出缓冲
      ob_start();
      header('Location: /' .ROOT_DIR . '/index.php');
      // 清理输出缓冲
     ob_end_clean();
      exit; // 退出脚本，防止后续输出
  } else {
      // 用户未找到
      echo "<h1>Error</h1>";
      echo "<p>minUser not found.</p>";
  }
}else {
require_once $theme_path . '/search.php';
require_once $theme_path . '/register.php';
require_once $theme_path . '/login.php';
require_once $theme_path . '/resetpassword.php';
require_once $theme_path . '/form.php';
$dbHandler = new DatabaseHandler($database);
$optionsDetails = $dbHandler->getAllOptions();
// 使用数组键访问代替对象属性访问
if(isset($optionsDetails['all_open']) && $optionsDetails['all_open'] === 'on' && (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true)){
   showMessage("已设置为未登录不显示，请登录！");
  //echo "<p>已设置为未登录不显示，请登录！</p>";
  exit;
}
try {
    $categoryLinks = $dbHandler->getLinksByCategory($user_id);
    ?>

<div class="row">
<div class="container">
 <?php   if (!empty($categoryLinks)) {
        foreach ($categoryLinks as $categoryName => $categoryLinksArray) {
        	// 注意：确保您的数据库查询确实返回了每个分类的公开状态，并且在此处正确获取
            $categoryIsPublic = $categoryLinksArray[0]['category_ispublic'] ?? 'off';
            $categoryID = $categoryLinksArray[0]['category_id'];
            $category_Nolink = $categoryLinksArray[0]['category_nolink'];
            if(isset($optionsDetails['category_open'])){
              $category_open = $optionsDetails['category_open'];
            }
            if(isset($optionsDetails['link_open'])){
              $link_open = $optionsDetails['link_open'];
            }
            // 如果分类公开，或者用户已登录，则显示该分类
            if (($categoryIsPublic === 'on'  && $category_Nolink !== 1 ) || (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) || $category_open === 'on') {
            ?>
            <!-- 主体内容 -->
                    <div class="singlecolumn">
                        <div class="categories-title" id="<?='category-'.htmlspecialchars($categoryID, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8') ?>
                        <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                        <div class="category_edit"><a href="#" class="opencategoryPopupForm_edit" data-category-id="<?= htmlspecialchars($categoryID, ENT_QUOTES, 'UTF-8') ?>">⚙</a>
                        </div>
                        <?php endif; ?>
                        </div>
                        <div class="contains">
                            <div class="grid-container">
                            <?php foreach ($categoryLinksArray as $link) {
                            	// 判断链接是否公开或用户是否已登录才显示
                                if ($link['ispublic'] === 'on' || (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)  || $link_open === 'on') {
                            	?>
                                <div class="card" data-tooltip="<?= htmlspecialchars($link['description'] ?? '暂无描述', ENT_QUOTES, 'UTF-8') ?>">
                                	<?php $colorCode = charToColorCode(mb_substr(htmlspecialchars($link['title'], ENT_QUOTES, 'UTF-8'), 0, 1, 'UTF-8')); ?>
                                    <div class="card-part left"><span style='color:<?php echo $colorCode ?>'><?php echo mb_substr(htmlspecialchars($link['title'], ENT_QUOTES, 'UTF-8'), 0, 1, 'UTF-8')?></span></div>
                                    <div class="card-part middle">
                                        <a href="<?= htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8') ?>"
                                          target="_blank"
                                          rel="noopener noreferrer">
                                            <?= htmlspecialchars($link['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </div>
                                    <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $link['id'] != null): ?>
                                    <div class="card-part right"><a href="#" class="openFormLink_edit" data-id="<?= htmlspecialchars($link['id'], ENT_QUOTES, 'UTF-8') ?>">⚙</a></div>
                                    <?php endif; ?>
                                    <div class="tooltip" style="display: none;"></div>
                                </div>
                            <?php
                            } //end if
                            } // End foreach for categoryLinksArray ?>
                            </div> <!-- End grid-container -->
                        </div> <!-- End contains -->
</div> <!-- End singlecolumn -->
            <?php
            } // end for categoryIsPublic
            } // End foreach for categoryLinks ?>
        <?php } else { ?>
            <p>No links found in the database.</p>
        <?php }
    } catch (Exception $e) {
        showMessage($e);
        ?>
</div> <!-- End container -->
</div> <!-- End row -->
        <?php
    }
require_once $theme_path . '/searchfast.php';
require_once $theme_path . '/showcategory.php';
require_once $theme_path . '/footer.php';
  }
?>
