<?php
define('HOST', 'http://' . $_SERVER['HTTP_HOST']);
define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);

if ((!isset($_SESSION['plurk_oauth_token']) || empty($_SESSION['plurk_oauth_token']))
	&& !isset($_GET['oauth_verifier']))
	include_once ROOT . DS . '_index.html';
else {
	include_once ROOT . DS . '_success.php';
	include_once ROOT . DS . '_success.html';
}
?>
