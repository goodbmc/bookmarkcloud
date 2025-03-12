window.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('register')) {
        document.getElementById('register').addEventListener('click', function(event) {
            event.preventDefault();
            showRegisterPopupForm();
        });
    }

    if (document.getElementById('registermobile')) {
        document.getElementById('registermobile').addEventListener('click', function(event) {
            event.preventDefault();
            showRegisterPopupForm();
        });
    }

    if (document.getElementById('cancelBtn_register')) {
        document.getElementById('cancelBtn_register').addEventListener('click', function() {
            hideRegisterPopupForm();
        });
    }

    // 背景遮罩点击关闭弹框
    const overlay = document.getElementById('registerPopupFormOverlay');
    overlay.addEventListener('click', () => {
        hideRegisterPopupForm();
    });
    
    function showRegisterPopupForm() {
        document.getElementById('registerPopupFormOverlay').style.display = 'block';
        document.getElementById('registerPopupForm').style.display = 'block';
    }

    function hideRegisterPopupForm() {
        document.getElementById('registerPopupFormOverlay').style.display = 'none';
        document.getElementById('registerPopupForm').style.display = 'none';
    }
});
