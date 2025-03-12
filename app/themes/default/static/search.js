window.addEventListener('DOMContentLoaded', function() {

	    // Category form popup related event listeners
    document.getElementById('search').addEventListener('click', function(event) {
        event.preventDefault();
        showSearchPopupForm();
    });

    document.getElementById('cancelBtn_search').addEventListener('click', function() {
        hideSearchPopupForm();
    });
    // 背景遮罩点击关闭弹框
    const overlay = document.getElementById('searchoverlay');
    overlay.addEventListener('click', () => {
            hideSearchPopupForm();
        });
    function showSearchPopupForm() {
        document.getElementById('searchoverlay').style.display = 'block';
        document.getElementById('searchPopupForm').style.display = 'block';
    }

    function hideSearchPopupForm() {
        document.getElementById('searchoverlay').style.display = 'none';
        document.getElementById('searchPopupForm').style.display = 'none';
    }
});
