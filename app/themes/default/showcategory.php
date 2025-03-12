<?php
if(!defined('ROOT')) {
    require_once '../../../path_root.php';
};
require_once __BMC_SYSTEM__ . 'functions.php';

if (!isset($_SESSION['user_id'])) {
    // 用户未登录时，可能需要重定向到登录页面或其他处理
    // 例如：
    // header('Location: login.php');
    exit;
}

$dbHandler = new DatabaseHandler($database);

try {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $categoryLinks = $dbHandler->getLinksByCategory($user_id);

    if (!empty($categoryLinks)) {
        // 菜单列表容器
    ?>
    <div id="categoryList" class="category-list">
        <ul>
            <?php foreach ($categoryLinks as $categoryName => $categoryLinksArray): ?>
                <?php
                $categoryIsPublic = $categoryLinksArray[0]['category_ispublic'] ?? 'off';
                $categoryID = $categoryLinksArray[0]['category_id'];
                $category_Nolink = $categoryLinksArray[0]['category_nolink'];

                // 如果分类公开，或者用户已登录，则显示该分类
                if (($categoryIsPublic == 'on' && $category_Nolink != 1) || (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)):
                ?>
                    <li>
                        <a href="#category-<?php echo htmlspecialchars($categoryID, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div> <!-- 关闭菜单列表容器 -->
    <?php
    }
} catch (Exception $e) {
    // 处理异常，例如记录错误或显示用户友好的消息
    ?>
    <p>Error: <?php echo htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>
    <?php
}
?>