<?php
$user_id = $_SESSION['user_id'];
$dbHandler = new DatabaseHandler($database);
$categories = $dbHandler->getAllCategories($user_id);
//var_dump($categories);
?>

<div id="overlay" class="overlay"></div>
<div id="popupForm" class="popupForm">
    <form id="linkForm" method="post">
        <input type="hidden" name="uid" id="uid" value="<?php echo $user_id; ?>">
        <label for="url">网址：</label>
        <div class="input-button-container">
        <input type="text" name="url" id="url" required>
        <button type="button" id="getMetadataBtn">获取</button>
        </div>
        <br>
        <label for="title">网站标题：</label>
        <input type="text" name="title" id="title" required>
        <br>
        <label for="description">描述：</label>
        <textarea name="description" id="description" rows="10">
        </textarea>
        <br>
        <label for="url">Favicon网址(可更改或上传，上传优先！)：</label>
        <input type="text" name="favicon" id="favicon">
        <br>
        <div class="input-button-container">
        <!-- 预览图片的位置 -->
        <div id="preview"></div>
        <input type="file" name="fileToUpload" id="fileToUpload" onchange="previewFile()">
        </div>
        <br>
        <label for="category">分类：</label>
        <div class="custom-select">
        <select name="category" id="category" required>
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
        <label for="ispublic">是否公开：</label>
        <div>
        <input type="checkbox" name="ispublic" id="ispublic" checked>
        </div>
        <label for="priority">优先级：</label>
        <div>
        <input type="text" name="priority" id="priority" maxlength="3" style="width: 50px;">
        </div>
        </div>
        <br>
        <div class="button-container">
        <button type="submit" id="submitlinkBtn" class="submitBtn">提交</button>
        <button type="button" id="cancelBtn" class="cancelBtn">取消</button>
        </div>
    </form>
</div>
<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/static/link.js"></script>
<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/action/link.js"></script>
<script src="app/server/api/js/get_metadata.js"></script>
