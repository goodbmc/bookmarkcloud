// 显示和隐藏菜单列表
    document.getElementById('categoryButton').addEventListener('click', function() {
        var menuList = document.getElementById('categoryList');
        if (menuList.style.display === 'block') {
            menuList.style.display = 'none';
        } else {
            menuList.style.display = 'block';
        }
    });

    // 当用户点击页面其他地方时关闭菜单
    window.addEventListener('click', function(event) {
        if (!event.target.matches('#categoryButton')) {
            var menuList = document.getElementById('categoryList');
            if (menuList.style.display === 'block') {
                menuList.style.display = 'none';
            }
        }
    });

    // 滚动到指定分类
    document.querySelectorAll('.category-list a').forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const targetId = this.getAttribute('href').substring(1); // 获取目标ID
            const targetElement = document.getElementById(targetId);
            if (targetElement) { // 距离顶部85px
                const offsetTop = targetElement.getBoundingClientRect().top + window.pageYOffset - 85;
                window.scrollTo({ top: offsetTop, behavior: 'smooth' });
            }
        });
    });