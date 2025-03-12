<?php
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}
?>

<div id="searchoverlay" class="overlay"></div>
<div id="searchPopupForm" class="popupForm">
    <form id="searchForm" method="post">
        <input type="hidden" name="uid" id="uid" value="<?php echo $user_id; ?>">
        <label for="keywords">关键词：</label>
        <input type="text" name="keywords" id="keywords" required>
        <div class='button-container'>
        <button type="submit" id="submitsearchBtn" class="submitBtn">搜索</button>
        <button type="button" id="cancelBtn_search" class="cancelBtn">取消</button>
        </div>
    </form>
</div>
<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/static/search.js"></script>
<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/action/search.js"></script>
