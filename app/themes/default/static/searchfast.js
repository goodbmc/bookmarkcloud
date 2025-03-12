document.addEventListener('DOMContentLoaded', (event) => {
    const toggleButton = document.getElementById('searchfastButton');
    const searchContainer = document.getElementById('searchContainer');
    const overlay = document.getElementById('searchfastoverlay');
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const cards = document.querySelectorAll('.card');

    let isSearchVisible = false;

    // 搜索框切换功能
    toggleButton.addEventListener('click', () => {
        isSearchVisible = !isSearchVisible;
        if (isSearchVisible) {
            searchContainer.classList.add('show');
            overlay.classList.add('show');
            searchInput.focus();  // 自动聚焦到输入框
        } else {
            searchContainer.classList.remove('show');
            overlay.classList.remove('show');
            searchInput.value = '';  // 清除输入框内容
            searchResults.innerHTML = '';  // 清除搜索结果
        }
    });

    // 背景遮罩点击关闭搜索框
    overlay.addEventListener('click', () => {
        isSearchVisible = false;
        searchContainer.classList.remove('show');
        overlay.classList.remove('show');
        searchInput.value = '';  // 清除输入框内容
        searchResults.innerHTML = '';  // 清除搜索结果
    });

    // 搜索框输入处理
    searchInput.addEventListener('input', () => {
        const filter = searchInput.value.trim().toUpperCase();
        searchResults.innerHTML = '';  // 清空之前的搜索结果

        if (filter === '') {
            // 如果输入框为空，清空搜索结果
            return;
        }

        // 创建一个新的结果容器
        const resultsContainer = document.createElement('div');
        resultsContainer.className = 'grid-container1';

        cards.forEach(card => {
            const title = card.querySelector('a').textContent.toUpperCase();
            if (title.includes(filter)) {
                // 创建一个新的结果项
                const resultItem = document.createElement('div');
                resultItem.className = 'card';
                resultItem.innerHTML = `
                    <div class="card" data-tooltip="${card.dataset.tooltip}">
                        <div class="card-part left"><span style="color:${card.querySelector('span').style.color}">${card.querySelector('span').textContent}</span></div>
                        <div class="card-part middle">
                            <a href="${card.querySelector('a').getAttribute('href')}"
                              target="_blank"
                              rel="noopener noreferrer">
                                ${card.querySelector('a').textContent}
                            </a>
                        </div>
                        <div class="tooltip" style="display: none;"></div> <!-- 添加一个空的 tooltip 元素 -->
                    </div>
                `;
                // 提取生成的 card 元素
                const cardElement = resultItem.querySelector('.card');
                resultsContainer.appendChild(cardElement);
            }
        });

        if (resultsContainer.childElementCount > 0) {
            searchResults.appendChild(resultsContainer);

            // 初始化新生成的卡片的 tooltip 功能
            const newCards = resultsContainer.querySelectorAll('.card');
            newCards.forEach(card => {
                const tooltipElement = card.querySelector('.tooltip');
                const tooltipText = card.getAttribute('data-tooltip');

                card.addEventListener('mouseenter', () => showTooltip(card, tooltipElement, tooltipText));
                card.addEventListener('mouseleave', () => hideTooltip(tooltipElement));
            });
        } else {
            searchResults.innerHTML = '<div class="no-results">No results found</div>';
        }
    });

    // Tooltip 显示与隐藏函数
   function showTooltip(card, tooltipElement, tooltipText) {
    const searchResults = document.getElementById('searchResults');
    if (!searchResults) {
        console.error("Could not find element with id 'searchResults'");
        return;
    }

    // 设置 tooltip 文本并显示
    tooltipElement.textContent = tooltipText;
    tooltipElement.style.display = 'block';

    // 获取 grid-container1 元素及其尺寸
    const containerRect = searchResults.getBoundingClientRect();
    const containerWidth = containerRect.width;
    const containerHeight = containerRect.height;

    // 获取卡片和 tooltip 的尺寸
    const cardRect = card.getBoundingClientRect();
    const tooltipRect = tooltipElement.getBoundingClientRect();
    const tooltipHeight = tooltipRect.height;
    const tooltipWidth = tooltipRect.width;

    // 计算 tooltip 的初始位置
    let tooltipTop = cardRect.bottom + 810;
    let tooltipLeft = cardRect.left;

    // 如果下方空间不足，则调整到卡片上方显示
    if (tooltipTop + tooltipHeight > containerHeight - 10) {
        tooltipTop = cardRect.top - tooltipHeight - 5;
    }

    // 确保 tooltip 不超出容器的左右边界
    if (tooltipLeft < 10) {
        tooltipLeft = 10;
    } else if (tooltipLeft + tooltipWidth > containerWidth - 10) {
        tooltipLeft = containerWidth - tooltipWidth - 10;
    }

    // 防止 tooltip 溢出容器顶部
    if (tooltipTop < 0) {
        tooltipTop = 0;
    }

    // 防止 tooltip 溢出容器底部
    if (tooltipTop + tooltipHeight > containerHeight) {
        tooltipTop = containerHeight - tooltipHeight;
    }

    // 设置 tooltip 的最终位置
    tooltipElement.style.top = `${tooltipTop}px`;
    tooltipElement.style.left = `${tooltipLeft}px`;

    // 再次检查是否仍然溢出底部
    const finalTooltipRect = tooltipElement.getBoundingClientRect();
    if (finalTooltipRect.bottom > containerHeight) {
        // 如果仍然溢出底部，尝试进一步调整
        tooltipTop = Math.max(0, containerHeight - tooltipHeight - 5);
        tooltipElement.style.top = `${tooltipTop}px`;
    }
}

    function hideTooltip(tooltipElement) {
        tooltipElement.style.display = 'none';
    }

    // 初始化已存在的卡片的 tooltip 功能
    cards.forEach(card => {
                const tooltipElement = card.querySelector('.tooltip');
                const tooltipText = card.getAttribute('data-tooltip');

                card.addEventListener('mouseenter', () => showTooltip(card, tooltipElement, tooltipText));
                card.addEventListener('mouseleave', () => hideTooltip(tooltipElement));
    });
});
