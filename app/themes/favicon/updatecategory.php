<?php
$user_id = $_SESSION['user_id'];
$dbHandler = new DatabaseHandler($database);
$categories = $dbHandler->getAllCategories($user_id);
?>
<div id="categoryPopupForm_editOverlay" class="overlay"></div>
<div id="categoryPopupForm_edit" class="popupForm">
    <form id="categoryForm_edit"  method="post">
        <!-- 编辑表单内容 -->
        <input type="hidden" name="id" id="id">
        <input type="hidden" name="uid" id="uid" value="<?php echo $user_id; ?>">
        <label for="category">分类名称：</label>
        <input type="text" name="category" id="category" required placeholder="请输入分类名称">
        <br>
        <div class="input-button-container">
        <label for="ispublic">是否公开：</label>
        <div>
        <input type="checkbox" name="ispublic" id="ispublic">
        </div>
        <label for="priority">优先级：</label>
        <div>
        <input type="text" name="priority" id="priority" maxlength="3" style="width: 50px;">
        </div>
        </div>

        <!-- 删除操作的按钮 -->
        <div class='button-container'>
            <button type="submit" id="submitcategory_editBtn" class="submitBtn">编辑</button>
            <button type="button" id="category_deleteBtn" class="deleteBtn">删除</button>
            <button type="button" id="cancelBtn_editcategory" class="cancelBtn">取消</button>
        </div>
    </form>
    </div>
    <!-- 删除确认模态 -->
    <div id="categoryPopupForm_deleteOverlay" class="overlay"></div>
    <div id="categoryPopupForm_delete" class="popupForm">
    	<form id="categoryForm_delete"  method="post">
        <input type="hidden" name="uid" id="uid" value="<?php echo $user_id; ?>">
        <h3>确认删除</h3>
        <p>您确定删除分类吗？所有链接将被转移到另一个分类。</p>
        <label for="newcategory">选择新的分类：</label>
        <div class="custom-select">
        <select name="newcategory" id="newcategory">
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['fid'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="select-arrow"></div>
        </div>
        <br>
        <br>
        <div class="input-button-container">
        <label for="delete_links">勾选下面选项，将删除该分类下所有链接！</label>
        <div>
        <input type="checkbox" name="delete_links" id="delete_links">
        </div>
        </div>
        <div class='button-container'>
        <button type="button" id="submitcategory_deleteBtn" class="deleteBtn">删除</button>
        <button type="button" id="cancelBtn_deletecategory" class="cancelBtn">取消</button>
        </div>
        </form>
    </div>

<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/static/updatecategory.js"></script>
<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/action/updatecategory.js"></script>
