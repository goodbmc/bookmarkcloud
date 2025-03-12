document.addEventListener('DOMContentLoaded', async function() {

    // 使用更通用的选择器来获取所有需要打开表单的链接
    const openFormLinks = document.querySelectorAll('[id^="openFormsetup"]');
    // 添加事件监听器到每个链接上
    openFormLinks.forEach(link => {
        link.addEventListener('click', async event => {
            event.preventDefault();

            // 显示弹窗
            showSetupPopupForm();

            try {
                const response = await fetch('app/server/api/apioptions.php');
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const linkData = await response.json();

                // 填充表单数据
                fillFormWithData('setupForm', linkData);
            } catch(error) {
                console.error('获取数据出错:', error);
            }
        });
    });

    // 取消按钮的事件监听器
    document.getElementById('cancelBtn_setup').addEventListener('click', hideSetupPopupForm);
    // 背景遮罩点击关闭弹框
    const overlay = document.getElementById('setupPopupFormOverlay');
    overlay.addEventListener('click', () => {
            hideSetupPopupForm();
        });
    // 显示弹窗的函数
    function showSetupPopupForm() {
        document.getElementById('setupPopupFormOverlay').style.display = 'block';
        document.getElementById('setupPopupForm').style.display = 'block';
    }

    // 隐藏弹窗的函数
    function hideSetupPopupForm() {
        document.getElementById('setupPopupFormOverlay').style.display = 'none';
        document.getElementById('setupPopupForm').style.display = 'none';
    }

    // 填充表单数据的函数，表单名称+数据源
    function fillFormWithData(formId, data) {
    console.log("接收到的数据:", data);
    let form = document.getElementById(formId);
    form.querySelector('#catergory_open').checked = data.category_open === 'on';
    form.querySelector('#link_open').checked = data.link_open === 'on';
    form.querySelector('#all_open').checked = data.all_open === 'on';
    // 其他表单项的填充逻辑...

    // 从data中获取themes并设置下拉列表的选中状态
    var themesSelect = document.getElementById('themes');
    for (let i = 0; i < themesSelect.options.length; i++) {
        let option = themesSelect.options[i];
        if (option.value === data.themes) {
            option.selected = true;
            break;
        }
    }
}

});
