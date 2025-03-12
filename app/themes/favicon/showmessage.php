<?php
function showMessage($message) {
    // Generate the message div
    $html = '<div id="alertPopup" class="alert-popup">' . htmlspecialchars($message) . '</div>';
    // Output the HTML
    echo $html;
}
?>
<script src="<?php echo htmlspecialchars($theme_path, ENT_QUOTES, 'UTF-8'); ?>/static/popup.js"></script>
