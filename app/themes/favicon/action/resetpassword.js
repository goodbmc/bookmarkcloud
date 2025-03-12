document.addEventListener('DOMContentLoaded', function() {
    var submitlinkBtn = document.getElementById('submitResetPasswordBtn');
    var form = document.getElementById('resetPasswordForm');
    var cancelBtn = document.getElementById('cancelBtn_reset');

    submitlinkBtn.addEventListener('click', function(e) {
        e.preventDefault(); // 阻止默认的点击行为（如果是提交按钮的话）

        var formData = new FormData(form); // 创建FormData对象
        var xhr = new XMLHttpRequest();
        xhr.open('POST','app/server/system/resetpassword.php', true);

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // 插入成功，跳转到index.php
                        window.customAlert.show('密码重置成功，重新登录！');
                        document.getElementById('resetPasswordFormOverlay').style.display = 'none';
                        document.getElementById('resetPasswordPopupForm').style.display = 'none';
                				document.getElementById('loginPopupFormOverlay').style.display = 'block';
                        document.getElementById('loginPopupForm').style.display = 'block';
                      //  window.location.href = 'index.php';
                    } else {
                        // 插入失败，显示错误信息
                        //alert(response.message);
                        window.customAlert.show(response.error);
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
