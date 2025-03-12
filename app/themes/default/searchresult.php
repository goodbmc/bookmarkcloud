<?php
if(!defined('ROOT')) {
    require_once '../../../path_root.php';
};
require_once __BMC_SYSTEM__ . 'functions.php';
require_once $theme_path . '/header.php';
require_once $theme_path . '/showmessage.php';
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
    $categoryLinks = $dbHandler->getLinksByCategoryKeywords($user_id,$keywords);
    ?>

<div class="row">
<div class="container">
 <?php   if (!empty($categoryLinks)) {
        foreach ($categoryLinks as $categoryName => $categoryLinksArray) {
        	// 注意：确保您的数据库查询确实返回了每个分类的公开状态，并且在此处正确获取
            $categoryIsPublic = $categoryLinksArray[0]['category_ispublic'] ?? 'off';
            $categoryID = $categoryLinksArray[0]['category_id'];
            if(isset($optionsDetails['category_open'])){
              $category_open = $optionsDetails['category_open'];
            }
            if(isset($optionsDetails['link_open'])){
              $link_open = $optionsDetails['link_open'];
            }
            // 如果分类公开，或者用户已登录，则显示该分类
            if ($categoryIsPublic == 'on' || (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) || $category_open === 'on') {
            ?>
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
                                if ($link['ispublic'] == 'on' || (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) || $link_open === 'on') {
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
                                    <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
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
require_once $theme_path . '/searchshowcategory.php';
require_once $theme_path . '/footer.php';
?>
