	document.addEventListener('DOMContentLoaded', function() {
    var submitlinkBtn = document.getElementById('submitlinkBtn');
    var form = document.getElementById('linkForm');
    var cancelBtn = document.getElementById('cancelBtn');

    submitlinkBtn.addEventListener('click', function(e) {
        e.preventDefault(); // 阻止默认的点击行为（如果是提交按钮的话）

        var formData = new FormData(form); // 创建FormData对象

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'app/server/system/link.php', true);

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
										console.log(xhr.responseText);
                    if (response.success) {
                        // 插入成功，跳转到index.php
                        window.location.href = 'index.php';
                    } else {
                        // 插入失败，显示错误信息
                        //alert(response.message);
												window.customAlert.show(response.message);
                    }
                } catch (e) {
                    console.error('响应数据解析失败: ', e);
                }
            } else {
                // 请求失败
                console.error('AJAX请求失败: ', xhr.status);
            }
        };

        xhr.onerror = function() {
            // 网络错误
            console.error('网络错误');
        };

        // 发送请求
        xhr.send(formData);
    });

    cancelBtn.addEventListener('click', function() {
        // 如果需要，添加取消按钮的逻辑
        // 例如：重置表单
        form.reset();
    });
});
