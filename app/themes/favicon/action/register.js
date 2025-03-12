document.addEventListener('DOMContentLoaded', function() {
    var submitlinkBtn = document.getElementById('submitregisterBtn');
    var form = document.getElementById('registerForm');
    var cancelBtn = document.getElementById('cancelBtn_register');

    submitlinkBtn.addEventListener('click', function(e) {
        e.preventDefault();

        var formData = new FormData(form);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'app/server/system/register.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                console.log('Response:', xhr.responseText); // 添加这一行
                try {
                    var response = JSON.parse(xhr.responseText);
                    console.log('Parsed Response:', response); // 添加这一行
                    if (response.success) {
                        console.log('Success detected, redirecting...'); // 添加这一行
                        window.location.href = 'index.php';
                    } else {
                        console.log('Failure detected:', response.message); // 添加这一行
                        //alert(response.message);
                        window.customAlert.show(response.message);
                    }
                } catch (e) {
                    console.error('响应数据解析失败: ', e);
                }
            } else {
                console.error('AJAX请求失败: ', xhr.status);
            }
        };

        xhr.onerror = function() {
            console.error('网络错误');
        };

        xhr.send(formData);
    });

    cancelBtn.addEventListener('click', function() {
        form.reset();
    });
});
