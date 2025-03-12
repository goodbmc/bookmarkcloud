<?php
$user_id = $_SESSION['user_id'];
$dbHandler = new DatabaseHandler($database);
$categories = $dbHandler->getAllCategories($user_id);
?>
<div id="categoryPopupFormOverlay" class="overlay"></div>
<div id="categoryPopupForm" class="popupForm">
    <form id="categoryForm" method="post">
        <input type="hidden" name="uid" id="uid" value="<?php echo $user_id; ?>">
        <label for="category">分类名称：</label>
        <input type="text" name="category" id="category" required placeholder="请输入分类名称">
        <br>
        <div class="input-button-container">
        <label for="ispublic">是否公开：</label>
        <div>
        <input type="checkbox" name="ispublic" id="ispublic" checked>
        </div>
        <label for="priority">优先级：</label>
        <div>
        <input type="text" name="priority" id="priority" maxlength="3" style="width: 50px;">
        </div>
        </div>
        <div class='button-container'>
        <button type="submit" id="submitcategoryBtn" class="submitBtn">提交</button>
        <button type="button" id="cancelBtn_addcategory" class="cancelBtn">取消</button>
        </div>
    </form>
</div>

<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/action/category.js"></script>
<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/static/category.js"></script>
