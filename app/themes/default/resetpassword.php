<div id="resetPasswordFormOverlay" class="overlay"></div>
<div id="resetPasswordPopupForm" class="popupForm">
    <form id="resetPasswordForm" method="post">
        <label for="email">邮箱：</label>
        <input type="email" name="email" id="email" required>
        <br>
        <label for="pin">PIN码：</label>
        <input type="text" name="pin" id="pin" required>
        <br>
        <label for="password">新密码：</label>
        <input type="password" name="password" id="password" required>
        <div class='button-container'>
        <button type="submit" id="submitResetPasswordBtn" class="submitBtn">提交</button>
        <button type="button" id="cancelBtn_reset" class="cancelBtn">取消</button>
        </div>
    </form>
</div>
<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/static/resetpassword.js"></script>
<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/action/resetpassword.js"></script>
