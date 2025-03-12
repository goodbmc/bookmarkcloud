<?php
$user_id = $_SESSION['user_id'];
$keywords = $_SESSION['keywords']??null;
if (!empty($keywords)){
	require_once 'searchresult.php';
	unset($_SESSION['keywords']);
} else{
	require_once 'indexnormal.php';
}
?>
