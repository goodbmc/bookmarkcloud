document.addEventListener('DOMContentLoaded', function() {
    // 定义一个函数来获取特定表单及其相关元素
    function getFormElements(formId, submitBtnId, cancelBtnId) {
        return {
            form: document.getElementById(formId),
            submitBtn: document.getElementById(submitBtnId),
            cancelBtn: document.getElementById(cancelBtnId)
        };
    }

    // 使用上述函数分别获取编辑和删除表单的元素
    var editFormElements = getFormElements('categoryForm_edit', 'submitcategory_editBtn', 'cancelBtn_editcategory');
    var deleteFormElements = getFormElements('categoryForm_delete', 'submitcategory_deleteBtn', 'cancelBtn_deletecategory');

    // 编辑表单的提交按钮事件处理
    editFormElements.submitBtn.addEventListener('click', function(e) {
        e.preventDefault();

        var formData = new FormData(editFormElements.form);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'app/server/system/updatecategory.php', true);

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        window.location.href = 'index.php';
                    } else {
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

    // 编辑表单的取消按钮事件处理
    editFormElements.cancelBtn.addEventListener('click', function() {
        editFormElements.form.reset();
    });

    // 删除表单的提交按钮事件处理
    deleteFormElements.submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        // 再次确认id字段的值
    console.log("ID before sending_test:", document.getElementById('id').value);
        // ... 删除表单的处理逻辑
      if (confirm('您确定要删除此分类吗？此操作不可撤销！')){
        var formData = new FormData(deleteFormElements.form);
        formData.append('id', document.getElementById('id').value);
 // 检查FormData对象的内容

    for (let pair of formData.entries()) {

        console.log('Key: ' + pair[0] + ', Value: ' + pair[1]);

    }
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'app/server/system/deletecategory.php', true);

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        window.location.href = 'index.php';
                    } else {
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
   }
    });

    // 删除表单的取消按钮事件处理
    deleteFormElements.cancelBtn.addEventListener('click', function() {
        deleteFormElements.form.reset();
    });
});
