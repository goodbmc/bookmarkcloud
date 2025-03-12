document.addEventListener('DOMContentLoaded', () => {
    initEventListeners();
});

async function initEventListeners() {
    const opencategoryFormLinks = document.querySelectorAll('.opencategoryPopupForm_edit');
    opencategoryFormLinks.forEach(link => {
        link.addEventListener('click', async event => {
            event.preventDefault();
            const itemId = link.getAttribute('data-category-id');
            await loadAndFillForm(itemId);

            // 在这里获取itemId并存储在一个全局变量中，以便稍后使用
            window.currentItemId = itemId;
        });
    });

    // 修改此行以传递itemId
    document.getElementById('category_deleteBtn').addEventListener('click', () => {
        showDeletecategoryPopupForm(window.currentItemId);
    });
    document.getElementById('cancelBtn_editcategory').addEventListener('click', hideCategoryPopupForm_edit);
    document.getElementById('cancelBtn_deletecategory').addEventListener('click', hideDeletecategoryPopupForm);
}
// 背景遮罩点击关闭弹框
const overlay = document.getElementById('categoryPopupForm_editOverlay');
overlay.addEventListener('click', () => {
        hideCategoryPopupForm_edit();
    });

const overlay_delete = document.getElementById('categoryPopupForm_deleteOverlay');
    overlay_delete.addEventListener('click', () => {
            hideDeletecategoryPopupForm();
        });

async function loadAndFillForm(itemId) {
    try {
        const response = await fetch(`app/server/api/apicategory.php?id=${itemId}`);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const linkData = await response.json();
        fillFormWithData('categoryForm_edit', linkData);
        showMainCategoryPopupForm_edit();
    } catch(error) {
        console.error('获取数据出错:', error);
    }
}

function showMainCategoryPopupForm_edit() {
    document.getElementById('categoryPopupForm_editOverlay').style.display = 'block';
    document.getElementById('categoryPopupForm_edit').style.display = 'block';
}

function hideCategoryPopupForm_edit() {
    document.getElementById('categoryPopupForm_editOverlay').style.display = 'none';
    document.getElementById('categoryPopupForm_edit').style.display = 'none';
}

function showDeletecategoryPopupForm(itemId) {
	  document.getElementById('id').value = itemId;
	  // 确认id字段已更新
    console.log("ID after setting:", document.getElementById('id').value);
    document.getElementById('categoryPopupForm_deleteOverlay').style.display = 'block';
    document.getElementById('categoryPopupForm_delete').style.display = 'block';
    document.getElementById('categoryPopupForm_editOverlay').style.display = 'none';
    document.getElementById('categoryPopupForm_edit').style.display = 'none';
}

function hideDeletecategoryPopupForm() {
    document.getElementById('categoryPopupForm_deleteOverlay').style.display = 'none';
    document.getElementById('categoryPopupForm_delete').style.display = 'none';
    document.getElementById('categoryPopupForm_editOverlay').style.display = 'block';
    document.getElementById('categoryPopupForm_edit').style.display = 'block';
}

function fillFormWithData(formId, data) {
    const form = document.getElementById(formId);
    form.querySelector('#category').value = data.name;
    form.querySelector('#ispublic').checked = data.ispublic === 'on';
    form.querySelector('#priority').value = data.priority || '0';
    form.querySelector('#id').value = data.id || '';
}

document.addEventListener('DOMContentLoaded', function() {
    // 获取checkbox和下拉框元素
    var deleteLinksCheckbox = document.getElementById('delete_links');
    var newCategorySelect = document.getElementById('newcategory');

    // 当checkbox的状态改变时触发事件处理函数
    deleteLinksCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // 如果checkbox被选中，禁用下拉框
            newCategorySelect.disabled = true;
        } else {
            // 如果checkbox未被选中，启用下拉框
            newCategorySelect.disabled = false;
        }
    });
});
