// customAlert.js

// 检查是否已经加载过此脚本，防止重复定义
if (window.customAlert) {
    console.warn('customAlert has already been defined.');
} else {
    window.customAlert = (function() {
        function showAlert(message) {
            document.getElementById('alertMessage').textContent = message;
            document.getElementById('customAlert').style.display = 'block';
            document.getElementById('customOverlay').style.display = 'block';

            // 点击 OK 后关闭弹窗
            document.getElementById('alertOkButton').onclick = function() {
                closeAlert();
            };

            // 点击遮罩层后关闭弹窗
            document.getElementById('customOverlay').onclick = function() {
                closeAlert();
            };
        }

        function closeAlert() {
            document.getElementById('customAlert').style.display = 'none';
            document.getElementById('customOverlay').style.display = 'none';
        }

        return {
            show: showAlert,
            hide: closeAlert
        };
    })();
}