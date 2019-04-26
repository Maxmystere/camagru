<?php
session_start();

require_once "header.php";



if (!$_SESSION['uname']) {
	echo "<h1 class='dreams' style='font-size: 30px;margin: 0;transform: none;position:unset;'>You must be logged in :(</h1>";
	require_once "login.php";
	return;
}

require_once "config/database.php";
	try {
		$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		die("Connection failed: Sorry !");
	}

function loadimg($pdo)
{
	$res = $pdo->query("SELECT * FROM photos WHERE `id_user` LIKE \"". $_SESSION['uid'] . "\" ORDER BY date DESC;");

	//echo ("<div class='flex-container'>");
	echo ("<div>");
	foreach ($res as $data) {
		echo "<div>";
		echo "<img src='data:image/png;base64, " . ($data['photo']) . "' alt='Img' onclick='window.location.href = \"/photo.php?pid=" . $data['pid'] . "\";'/>";
		echo "</div>";
	}
	echo ("</div>");
}

function loadmeme($pdo)
{
	$res = $pdo->query("SELECT * FROM meme ORDER BY date DESC;");

	foreach ($res as $data) {
		echo "<div>";
		echo "<img id='meme" . $data['mid'] . "' src='data:image/png;base64, " . ($data['photo']) . "' alt='Img' onclick='imgclick(" . $data['mid'] . ")'/>";
		echo "</div>";
	}
}

echo "<script type='text/javascript' src='photomaker.js'></script>";

echo "<div class='form-popup'>";
echo "<button type='button' onclick=\"document.getElementById('login-form').style.display = (document.getElementById('login-form').style.display == 'block' ? 'none' : 'block')\">+</button>";
require_once "login.php";
echo "</div>";

?>

<div class='makercontainer'>
	<div class='maker'>
		<video autoplay='true' style='display: none;' id='videoElement'></video>
		<canvas id="videoCanvas" width=640 height=480></canvas><br />
		<input style='display: none;' type="file" id="fileInput">
		<button type='button' style="margin: 10px;" id='capture' disabled>📷</button><br />
		
		<canvas style='display: none;' id="videoSnapshot" width=640 height=480></canvas>
		<div class='chooser'>
			<?PHP loadmeme($pdo) ?>
		</div>
	</div>
	<div class='shower'>
		<?PHP loadimg($pdo) ?>
	</div>
</div>
