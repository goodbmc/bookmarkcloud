<?php
if(!defined('ROOT')) {
    require_once '../../../path_root.php';
}
require_once  ROOT . '/app/server/api/get_themes.php';

// 直接调用 getSubdirectories 函数,获取主题名称
$result = getThemes();


$user_id = $_SESSION['user_id'];
?>
<div id="setupPopupFormOverlay" class="overlay"></div>
<div id="setupPopupForm" class="popupForm">
    <form id="setupForm" method="post">
        <input type="hidden" name="uid" id="uid" value="<?php echo $user_id; ?>">
        <label for="username">用户名：</label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly required>
        <br>
        <label for="password">输入当前密码：</label>
        <input type="password" name="password" id="password" required>
        <br>
        <label for="password">输入新密码（为空时不修改）：</label>
        <input type="password" name="newpassword" id="newpassword">
        <br>
        <label for="email">输入新邮箱（为空时不修改）：</label>
        <input type="email" name="email" id="email">
        <br>
        <label>未登录时是否显示未公开数据：</label>
        <div class="input-button-container">
        <label for="catergory_open">分类：</label>
        <div>
        <input type="checkbox" name="catergory_open" id="catergory_open">
        </div>
        <label for="link_open">链接：</label>
        <div>
        <input type="checkbox" name="link_open" id="link_open">
        </div>
        <label for="all_open">均不显示（含公开）：</label>
        <div>
        <input type="checkbox" name="all_open" id="all_open">
        </div>
        </div>
        <br>
        <label for="themes">选择主题：</label>
        <div class="custom-select">
        <select name="themes" id="themes" required>
            <?php foreach ($result as $theme): ?>
            <option value="<?= htmlspecialchars($theme, ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($theme, ENT_QUOTES, 'UTF-8') ?>
          </option>
           <?php endforeach; ?>
         </select>
         <div class="select-arrow"></div>
         </div>
         <br>
         <button id="pinTextButton">显示PIN码</button>
         <p id="pinText"></p>
        <div class='button-container'>
        <button type="submit" id="submitsetupBtn" class="submitBtn">提交</button>
        <button type="button" id="cancelBtn_setup" class="cancelBtn">取消</button>
        <button type="button" id="updatePinBtn" class="updatePinBtn">刷新</button>
        </div>
    </form>
</div>

<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/static/setup.js"></script>
<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/action/setup.js"></script>
