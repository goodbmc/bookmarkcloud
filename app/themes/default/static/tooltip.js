document.addEventListener('DOMContentLoaded', function() {
    var cards = document.querySelectorAll('.card');

    function showTooltip(card, tooltipElement, tooltipText) {
        tooltipElement.textContent = tooltipText;
        tooltipElement.style.display = 'block';

        var cardRect = card.getBoundingClientRect();
        var tooltipRect = tooltipElement.getBoundingClientRect();
        var windowHeight = window.innerHeight;
        var tooltipHeight = tooltipRect.height;

        // 默认尝试在卡片上方显示
        var tooltipTop = cardRect.top - tooltipHeight - 10;
        
        // 如果上方空间不足，则调整到底部显示
        if (tooltipTop < 10) { // 确保距离顶部至少10px
            tooltipTop = cardRect.bottom + 10;
        }

        // 计算左侧位置，确保tooltip居中且不溢出
        var tooltipLeft = cardRect.left + (cardRect.width - tooltipRect.width) / 2;
        // 防止左侧溢出
        tooltipLeft = Math.max(tooltipLeft, 10);
        // 防止右侧溢出
        tooltipLeft = Math.min(tooltipLeft, window.innerWidth - tooltipRect.width - 10);

        tooltipElement.style.top = tooltipTop + 'px';
        tooltipElement.style.left = tooltipLeft + 'px';
    }

    function hideTooltip(tooltipElement) {
        tooltipElement.style.display = 'none';
    }

    cards.forEach(function(card) {
        var tooltipElement = card.querySelector('.tooltip');
        var tooltipText = card.getAttribute('data-tooltip'); 

        card.addEventListener('mouseenter', function(event) {
            showTooltip(card, tooltipElement, tooltipText);
        });

        card.addEventListener('mouseleave', function() {
            hideTooltip(tooltipElement);
        });
    });
});