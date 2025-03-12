window.addEventListener('DOMContentLoaded', function() {


    // Form popup related event listeners
    document.getElementById('openFormLink').addEventListener('click', function(event) {
        event.preventDefault();
        showMainPopupForm();
    });

    document.getElementById('openFormLinkmobile').addEventListener('click', function(event) {
        event.preventDefault();
        showMainPopupForm();
    });

    // Common cancel button event listener
    document.getElementById('cancelBtn').addEventListener('click', function() {
        hideMainPopupForm();
        // hideCategoryPopupForm();
    });
    // 背景遮罩点击关闭弹框
    const overlay = document.getElementById('overlay');
    overlay.addEventListener('click', () => {
            hideMainPopupForm();
        });

    function showMainPopupForm() {
        document.getElementById('overlay').style.display = 'block';
        document.getElementById('popupForm').style.display = 'block';
        // 获取textarea元素并清空其内容
        var descriptionTextarea = document.getElementById("description");
        descriptionTextarea.value = "";
    }

    function hideMainPopupForm() {
        document.getElementById('overlay').style.display = 'none';
        document.getElementById('popupForm').style.display = 'none';
    }

    // 获取textarea元素并清空其内容
    //var descriptionTextarea = document.getElementById("description");
    //descriptionTextarea.value = "";
    
    function updateFaviconPreview(faviconUrl, previewElement) {
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

    document.getElementById('getMetadataBtn').addEventListener('click', async function() {
    	
    	  // 假设 getMetadata 是一个返回 Promise 的异步函数
        await getMetadata();  // 等待 getMetadata 完成
        // 模拟等待 getMetadata 完成，等待1秒
        //await new Promise(resolve => setTimeout(resolve, 5000));
        // 获取favicon输入框的值
        const faviconInput = document.getElementById('favicon');
        console.log('Favicon URL:', faviconInput); // 调试输出
        console.log('Favicon URL:', faviconInput.value.trim()); // 调试输出
        const faviconUrl = faviconInput.value.trim();
        const previewElement = document.getElementById('preview');
        console.log('Favicon URL:', faviconUrl); // 调试输出

        if (faviconUrl) {
           updateFaviconPreview(faviconUrl, previewElement);
            }
            
    });
            
            
    // 监听 favicon_edit 的变化

    document.getElementById('favicon').addEventListener('input', function() {
        const faviconUrl = this.value.trim(); // 获取并去除用户输入的URL值两端的空格
        const previewElement = document.getElementById('preview'); // 获取预览元素

        updateFaviconPreview(faviconUrl, previewElement); // 调用更新函数
    });

    

  function previewFile(inputId, previewId) {
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
  document.getElementById('fileToUpload').addEventListener('change', function () {
      previewFile('#fileToUpload', '#preview');
  });
});
