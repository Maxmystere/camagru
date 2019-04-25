<?php
session_start();

require_once "header.php";



if (!$_SESSION['uname']) {
	echo "<h1 class='dreams' style='font-size: 30px;margin: 0;transform: none;position:unset;'>You must be logged in :(</h1>";
	require_once "login.php";
	return;
}

echo "<div class='form-popup'>";
echo "<button type='button' onclick=\"document.getElementById('login-form').style.display = (document.getElementById('login-form').style.display == 'block' ? 'none' : 'block')\">+</button>";
require_once "login.php";
echo "</div>";

?>
