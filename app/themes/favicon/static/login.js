window.addEventListener('DOMContentLoaded', function() {

	    // Category form popup related event listeners
	if (document.getElementById('login')) {
    document.getElementById('login').addEventListener('click', function(event) {
        event.preventDefault();
        showRegisterPopupForm();
    });
	}
  if (document.getElementById('loginmobile')) {
    document.getElementById('loginmobile').addEventListener('click', function(event) {
        event.preventDefault();
        showRegisterPopupForm();
    });
	}
  if (document.getElementById('cancelBtn_login')) {
    document.getElementById('cancelBtn_login').addEventListener('click', function() {
        hideRegisterPopupForm();
    });
	}
	// 背景遮罩点击关闭弹框
	const overlay = document.getElementById('loginPopupFormOverlay');
	overlay.addEventListener('click', () => {
					hideRegisterPopupForm();
			});

    function showRegisterPopupForm() {
        document.getElementById('loginPopupFormOverlay').style.display = 'block';
        document.getElementById('loginPopupForm').style.display = 'block';
    }

    function hideRegisterPopupForm() {
        document.getElementById('loginPopupFormOverlay').style.display = 'none';
        document.getElementById('loginPopupForm').style.display = 'none';
    }
});
