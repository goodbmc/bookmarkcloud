<?php
$user_id = $_SESSION['user_id'];
$dbHandler = new DatabaseHandler($database);
$categories = $dbHandler->getAllCategories($user_id);
//var_dump($categories);
?>

<div id="overlay_edit" class="overlay"></div>
<div id="popupForm_edit" class="popupForm">
    <form id="linkForm_edit" method="post" enctype="multipart/form-data">
        <input type="hidden" name="uid" id="uid" value="<?php echo $user_id; ?>">
    	  <input type="hidden" name="id" id="id_edit">
        <label for="url">网址：</label>
        <div class="input-button-container">
        <input type="text" name="url" id="url_edit" required>
        <button type="button" id="get_metadata_editBtn">获取</button>
        </div>
        <br>
        <label for="title">网站标题：</label>
        <input type="text" name="title" id="title_edit" required>
        <br>
        <label for="description">描述：</label>
        <textarea name="description" id="description_edit" rows="10">
        </textarea>
        <br>
        <label for="url">Favicon网址(可更改或上传，上传优先！)：</label>
        <input type="text" name="favicon" id="favicon_edit">
        <br>
        <div class="input-button-container">
        <!-- 预览图片的位置 -->
        <div id="preview_edit"></div>
        <input type="file" name="fileToUpload" id="fileToUpload_edit" onchange="previewFile_edit()">
        </div>
        <br>
        <label for="category">分类：</label>
        <div class="custom-select">
        <select name="category" id="category_edit" required>
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
        <input type="checkbox" name="ispublic" id="ispublic_edit" checked>
        </div>
        <label for="priority">优先级：</label>
        <div>
        <input type="text" name="priority" id="priority_edit" maxlength="3" style="width: 50px;">
        </div>
        </div>
        <br>
        <div class="button-container">
        <button type="submit" id="submitlinkBtn_edit" class="submitBtn">提交</button>
        <button type="submit" id="deletelinkBtn_edit" class="deleteBtn">删除</button>
        <button type="button" id="cancelBtn_edit" class="cancelBtn">取消</button>
        </div>
    </form>
</div>

<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/static/updatelink.js"></script>
<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/action/updatelink.js"></script>
<script src="app/server/api/js/get_metadata_edit.js"></script>
