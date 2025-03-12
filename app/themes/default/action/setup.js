	document.addEventListener('DOMContentLoaded', function() {
    var submitlinkBtn = document.getElementById('submitsetupBtn');
    var form = document.getElementById('setupForm');
    var cancelBtn = document.getElementById('cancelBtn_setup');
		var pinTextButton = document.getElementById('pinTextButton');
    var updatePinBtn = document.getElementById('updatePinBtn');

    submitlinkBtn.addEventListener('click', function(e) {
        e.preventDefault(); // 阻止默认的点击行为（如果是提交按钮的话）

        var formData = new FormData(form); // 创建FormData对象

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'app/server/system/setup.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
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

    pinTextButton.addEventListener('click', function(e) {
	    // 阻止默认行为（如果有的话）
	    e.preventDefault();

	    var xhr = new XMLHttpRequest();
	    xhr.open("GET", "app/server/api/apipin.php?action=getPin", true);
	    xhr.onreadystatechange = function () {
	        if (xhr.readyState == 4 && xhr.status == 200) {
	            try {
	                var response = JSON.parse(xhr.responseText);

	                // 假设返回的JSON包含一个名为'pin'的字段
	                if(response.pin){
	                    var button = document.getElementById('pinTextButton');
											var updatebutton = document.getElementById('updatePinBtn');
	                    var text = document.getElementById('pinText');

	                    // 设置显示的文本内容为返回的PIN码
	                    text.textContent = "您的PIN码是：" + response.pin +"，点击刷新可生成新的！";

	                    // 隐藏按钮并显示文本
	                    button.style.display = "none";
											updatebutton.style.display = "block";
	                    text.style.display = "block";
	                } else {
	                    alert("未找到PIN码");
	                }
	            } catch (error) {
	                console.error("Error parsing JSON response:", error);
	                alert("验证未通过");
	            }
	        }
	    };
	    xhr.send();
	});

	updatePinBtn.addEventListener('click', function(e) {
		// 阻止默认行为（如果有的话）
		e.preventDefault();
		var xhr = new XMLHttpRequest();
		xhr.open("GET", "app/server/api/apipin.php?action=updatePin", true);
		xhr.onreadystatechange = function () {
				if (xhr.readyState == 4 && xhr.status == 200) {
						try {
								var response = JSON.parse(xhr.responseText);

								// 假设返回的JSON包含一个名为'pin'的字段
								if(response.pin){
									  var pinButton = document.getElementById('pinTextButton');
										var button = document.getElementById('updatePinBtn');
										var text = document.getElementById('pinText');

										// 设置显示的文本内容为返回的PIN码
										text.textContent = "您的PIN码是：" + response.pin +"，点击刷新可生成新的！";

										// 隐藏按钮并显示文本
										pinButton.style.display = "none";
										text.style.display = "block";
								} else {
										alert("未找到PIN码");
								}
						} catch (error) {
								console.error("Error parsing JSON response:", error);
								alert("验证未通过");
						}
				}
		};
		xhr.send();
});
    cancelBtn.addEventListener('click', function() {
        // 如果需要，添加取消按钮的逻辑
        // 例如：重置表单
        form.reset();
    });
});
