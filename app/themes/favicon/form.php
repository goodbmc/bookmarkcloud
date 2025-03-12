<?php
if ((isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)) {
require_once $theme_path . '/link.php';
require_once $theme_path . '/updatelink.php';
require_once $theme_path . '/updatecategory.php';
require_once $theme_path . '/category.php';
require_once $theme_path . '/setup.php';
}
?>
