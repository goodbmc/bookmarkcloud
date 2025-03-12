function getMetadata_edit() {
    return new Promise((resolve, reject) => {
        var url = document.getElementById('url_edit').value;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'app/server/api/get_metadata_favicon.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onerror = function() {
            console.error('请求失败');
            alert('请求过程中发生错误！');
            reject(new Error('Network request failed'));
        };

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var response;
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (e) {
                        console.error('解析JSON失败', e);
                        alert('数据解析错误');
                        reject(e);
                        return;
                    }

                    if (response.title) {
                        document.getElementById('title_edit').value = response.title;
                    } else {
                        document.getElementById('title_edit').value = ''; // 清空标题以防上次请求的结果
                    }

                    if (response.description) {
                        document.getElementById('description_edit').value = response.description;
                    } else { 
                        document.getElementById('description_edit').value = ''; // 清空描述以防上次请求的结果
                    }

                    if (response.favicon) {
                        document.getElementById('favicon_edit').value = response.favicon;
                    } else { 
                        document.getElementById('favicon_edit').value = ''; // 清空favicon以防上次请求的结果
                    }
                    
                    if (response.error && !response.error.includes('描述')) {
                        alert(response.error);
                    }
                    resolve(response);
                } else {
                    console.error('服务器返回错误状态:', xhr.status);
                    alert('服务器错误(无法获取)！');
                    reject(new Error(`Server responded with status: ${xhr.status}`));
                }
            }
        };

        xhr.send('url=' + encodeURIComponent(url));
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('get_metadata_editBtn').addEventListener('click', getMetadata_edit);
});
