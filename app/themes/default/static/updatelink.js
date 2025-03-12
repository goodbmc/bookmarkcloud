window.addEventListener('DOMContentLoaded', function() {
    // 修改为使用类名选择元素并添加事件监听器
    var openFormLinks = document.querySelectorAll('.openFormLink_edit');
    openFormLinks.forEach(function(link) {
        link.addEventListener('click', async function(event) {
            event.preventDefault();

            // 获取当前点击元素的data-id
            var itemId = this.getAttribute('data-id');

            // 显示弹窗
            showMainPopupForm_edit();

            // 异步获取数据并填充表单
            try {
                const response = await fetch(`app/server/api/api.php?id=${itemId}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const linkData = await response.json();
                // 填充表单数据
                fillFormWithData('linkForm_edit',linkData);
            } catch(error) {
                console.error('获取数据出错:', error);
            }
        });
    });

    // Common cancel button event listener
    document.getElementById('cancelBtn_edit').addEventListener('click', function() {
        hideMainPopupForm_edit();
        //showMainPopupForm_edit();
    });
    // 背景遮罩点击关闭弹框
    const overlay = document.getElementById('overlay_edit');
    overlay.addEventListener('click', () => {
            hideMainPopupForm_edit();
        });
    function showMainPopupForm_edit() {
        document.getElementById('overlay_edit').style.display = 'block';
        document.getElementById('popupForm_edit').style.display = 'block';
    }

    function hideMainPopupForm_edit() {
        document.getElementById('overlay_edit').style.display = 'none';
        document.getElementById('popupForm_edit').style.display = 'none';
    }

    // 填充表单数据的函数
    function fillFormWithData(formId,data) {
    //console.log("Received data:", data); // 打印接收到的数据，用于调试
    const form = document.getElementById(formId);
    document.getElementById('title_edit').value = data.title || ''; // 确保title元素存在
    //console.log("Before setting title:", document.getElementById('title_edit').value);
    document.getElementById('url_edit').value = data.url || ''; // 确保url元素存在
    document.getElementById('description_edit').value = data.description || ''; // 确保description元素存在
    document.getElementById('favicon_edit').value = data.favicon || '';
    var faviconPreview = document.querySelector('#preview_edit');
        if (data.favicon_image) {
            faviconPreview.innerHTML = `<img src="app/uploads/${data.favicon_image}"/>`;
        } else {
            faviconPreview.innerHTML = `<img src="app/uploads/default.png"/>`;
        }
    document.getElementById('ispublic_edit').checked = data.ispublic === 'on'|| null; // 根据实际情况调整
    document.getElementById('priority_edit').value = data.priority || '0'; // 确保priority元素存在
    document.getElementById('id_edit').value = data.id || null;
    // 其他表单项的填充逻辑...
    //console.log("After setting title:", document.getElementById('title_edit').value);
    // 从data中获取fid并设置下拉列表的选中状态
    var categorySelect = document.getElementById('category_edit');
    for (let i = 0; i < categorySelect.options.length; i++) {
        let option = categorySelect.options[i];
        if (option.value === data.fid.toString()) {
            option.selected = true;
            break;
        }
    }
}

function updateFaviconPreview_edit(faviconUrl, previewElement) {
    console.log("Updating favicon preview with URL:", faviconUrl); // 调试信息

    // 清除旧的内容
    previewElement.innerHTML = '';

    // 检查URL是否为空
    if (!faviconUrl) {
        console.log("URL is empty"); // 调试信息
        return;
    }

    // 创建新的img元素
    const img = document.createElement('img');
    img.src = faviconUrl;

   // img.onload = function() {
    //    console.log("Image loaded successfully"); // 调试信息
   // };

    img.onerror = function() {
        //console.log("Failed to load image"); // 调试信息
        this.onerror = null; // 防止进入死循环
        // 显示错误信息
        //const errorMsg = document.createElement('div');
        //errorMsg.textContent = 'Failed';
        //errorMsg.style.color = 'red';
        //previewElement.appendChild(errorMsg);
    };

    // 添加到预览区域
    previewElement.appendChild(img);
}

 document.getElementById('get_metadata_editBtn').addEventListener('click', async function() {
        // 获取favicon输入框的值
        // 假设 getMetadata_edit 是一个返回 Promise 的异步函数
        await getMetadata_edit();  // 等待 getMetadata_edit 完成
        const faviconInput = document.getElementById('favicon_edit');
        const faviconUrl = faviconInput.value.trim();
        const previewElement = document.getElementById('preview_edit');
        console.log('Favicon URL:', faviconUrl); // 调试输出

        if (faviconUrl) {
           updateFaviconPreview_edit(faviconUrl, previewElement);
            }
            
    });
    
// 监听 favicon_edit 的变化
document.getElementById('favicon_edit').addEventListener('input', function() {
    const faviconUrl = this.value.trim(); // 获取并去除用户输入的URL值两端的空格
    const previewElement = document.getElementById('preview_edit'); // 获取预览元素

    updateFaviconPreview_edit(faviconUrl, previewElement); // 调用更新函数
});



function previewFile_edit(inputId, previewId) {
    const preview = document.querySelector(previewId);
    const file = document.querySelector(inputId).files[0];
    const reader = new FileReader();

    reader.addEventListener("load", function () {
        preview.innerHTML = '';
        const img = document.createElement('img');
        img.setAttribute('src', reader.result);
        preview.appendChild(img);
    }, false);

    if (file) {
        reader.readAsDataURL(file);
    }
}

// 绑定事件监听器
document.getElementById('fileToUpload_edit').addEventListener('change', function () {
    previewFile_edit('#fileToUpload_edit', '#preview_edit');
});

});
