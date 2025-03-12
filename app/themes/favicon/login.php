<div id="loginPopupFormOverlay" class="overlay"></div>
<div id="loginPopupForm" class="popupForm">
    <form id="loginForm" method="post">
        <label for="username">用户名：</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">密码：</label>
        <input type="password" name="password" id="password" required>
        <div class='button-container'>
        <button type="submit" id="submitloginBtn" class="submitBtn">登录</button>
        <button type="button" id="cancelBtn_login" class="cancelBtn">取消</button>
        </div>
    </form>
        <!-- 添加忘记密码链接 -->
        <a href="#" id="resetPasswordLink" class = "resetPasswordLink">忘记密码？</a>
</div>
<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/static/login.js"></script>
<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/action/login.js"></script>
