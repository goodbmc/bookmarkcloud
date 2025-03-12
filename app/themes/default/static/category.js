window.addEventListener('DOMContentLoaded', function() {
    // Category form popup related event listeners
    document.getElementById('addCategory').addEventListener('click', function(event) {
        event.preventDefault();
        showCategoryPopupForm();
    });

    document.getElementById('addCategorymobile').addEventListener('click', function(event) {
        event.preventDefault();
        showCategoryPopupForm();
    });

    // Common cancel button event listener
    document.getElementById('cancelBtn_addcategory').addEventListener('click', function() {
        hideCategoryPopupForm();
    });
    // 背景遮罩点击关闭弹框
    const overlay = document.getElementById('categoryPopupFormOverlay');
    overlay.addEventListener('click', () => {
            hideCategoryPopupForm();
        });
    function showCategoryPopupForm() {
        document.getElementById('categoryPopupFormOverlay').style.display = 'block';
        document.getElementById('categoryPopupForm').style.display = 'block';
    }

    function hideCategoryPopupForm() {
        document.getElementById('categoryPopupFormOverlay').style.display = 'none';
        document.getElementById('categoryPopupForm').style.display = 'none';
    }


});
