window.addEventListener('DOMContentLoaded', function() {

	    // Category form popup related event listeners
	if (document.getElementById('resetPasswordLink')) {
    document.getElementById('resetPasswordLink').addEventListener('click', function(event) {
        event.preventDefault();
        showResetPassWordPopupForm();
    });
	}

  if (document.getElementById('cancelBtn_reset')) {
    document.getElementById('cancelBtn_reset').addEventListener('click', function() {
        hideResetPassWordPopupForm();
    });
	}
	// 背景遮罩点击关闭弹框
	const overlay = document.getElementById('resetPasswordFormOverlay');
	overlay.addEventListener('click', () => {
					hideResetPassWordPopupForm();
			});

    function showResetPassWordPopupForm() {
        document.getElementById('resetPasswordFormOverlay').style.display = 'block';
        document.getElementById('resetPasswordPopupForm').style.display = 'block';
				document.getElementById('loginPopupFormOverlay').style.display = 'none';
        document.getElementById('loginPopupForm').style.display = 'none';
    }

    function hideResetPassWordPopupForm() {
        document.getElementById('resetPasswordFormOverlay').style.display = 'none';
        document.getElementById('resetPasswordPopupForm').style.display = 'none';
				document.getElementById('loginPopupFormOverlay').style.display = 'block';
        document.getElementById('loginPopupForm').style.display = 'block';
    }
});
