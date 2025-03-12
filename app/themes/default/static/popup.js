// 显示弹窗
function showPopup() {
    var alertPopup = document.getElementById('alertPopup');
    if (alertPopup) {
        alertPopup.style.display = 'block';
    }
}
// 隐藏弹窗
function hidePopup() {
    var alertPopup = document.getElementById('alertPopup');
    if (alertPopup) {
        alertPopup.style.display = 'none';
    }
}
// 在页面加载完成后根据条件显示弹窗
document.addEventListener('DOMContentLoaded', function() {
    // 假设这里有一个条件变量，例如 isPopupRequired
    var isPopupRequired = true; // 示例条件

    if (isPopupRequired) {
        showPopup();
        setTimeout(hidePopup, 3000); // 3秒后隐藏弹窗
    }
});
