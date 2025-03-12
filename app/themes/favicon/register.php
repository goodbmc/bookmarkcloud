<div id="registerPopupFormOverlay"  class="overlay"></div>
<div id="registerPopupForm" class="popupForm">
    <form id="registerForm" method="post">
        <label for="username">用户名：</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">密码：</label>
        <input type="password" name="password" id="password" required>
        <br>
        <label for="email">邮箱：</label>
        <input type="email" name="email" id="email" required>
        <div class='button-container'>
        <button type="submit" id="submitregisterBtn" class="submitBtn">提交</button>
        <button type="button" id="cancelBtn_register" class="cancelBtn">取消</button>
        </div>
    </form>
</div>

<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/static/register.js"></script>
<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/action/register.js"></script>
