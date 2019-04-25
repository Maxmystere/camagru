<?php
session_start();
if (isset($_GET['page']) && !is_numeric($_GET['page']))
{
	header("Location: index.php");
	exit;
}

require_once "config/database.php";

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Connection failed: Sorry !");
}

if (isset($_GET['page']))
{
	$res = $pdo->query("SELECT * FROM photos ORDER BY date DESC LIMIT " . (($_GET['page'] - 1) * 15) . ", 15;");
	if (!$res->rowCount())
	{
		header("Location: /index.php?page=" . ($_GET['page'] - 1));
		exit;
	}
}
else
	$res = $pdo->query("SELECT * FROM photos ORDER BY date DESC LIMIT 15;");

require_once "header.php";

echo "<div class='form-popup'>";
echo "<button type='button' onclick=\"document.getElementById('login-form').style.display = (document.getElementById('login-form').style.display == 'block' ? 'none' : 'block')\">+</button>";
require_once "login.php";
echo "</div>";



echo ("<div class='flex-container'>");
foreach ($res as $data) {
	$user = $pdo->query("SELECT (`username`) FROM userlist WHERE `uid` LIKE \"" . $data['id_user'] . "\";")->fetch();
	$likes = $pdo->query("SELECT COUNT(*) FROM likes WHERE `id_photo` LIKE \"" . $data['pid'] . "\";")->fetch()[0];
	echo "<div>";
	echo "<img src='data:image/png;base64, " . ($data['photo']) . "' alt='Img' onclick='window.location.href = \"/photo.php?pid=" . $data['pid'] . "\";'/>";
	echo "<div id='liketxt" . $data['pid'] . "' style='width: 100%;float: left;top: 10px;' class='liketxt'>" . $likes . " ♥</div>";
	echo "<div style='width: 30%;float: right;'>by " . $user['username'] . " " . "</div>";
	if (isset($_SESSION['uid'])) {
		echo "<form style='width: 30%;float: left;' class='likeform'>";
		echo "<input type='hidden' name='pid' value='" . $data['pid'] . "' />";
		if ($pdo->query("SELECT COUNT(*) FROM likes WHERE `id_photo` LIKE \"" . $data['pid'] . "\" AND id_user LIKE \"" . $_SESSION['uid'] . "\";")->fetch()[0])
			echo " <input type='submit' id='likebtn" . $data['pid'] . "' class='likebtn' name='UP' value='♥' style='background-color: red;border-radius: 50%;' />";
		else
			echo " <input type='submit' id='likebtn" . $data['pid'] . "' class='likebtn' name='UP' value='♥' style='background-color: white;' />";
		echo "</form>";
	}
	echo "</div>";
}
echo ("</div>");

echo "<div style='text-align: center;'>";
if (isset($_GET['page']) && $_GET['page'] > 1)
{
	echo "<button type='button' onclick=\"window.location='/index.php?page=" . ($_GET['page'] - 1) . "'\">←</button>";
	$res = $pdo->query("SELECT * FROM photos ORDER BY date DESC LIMIT " . (($_GET['page']) * 15) . ", 15;");
	if ($res->rowCount())
		echo "<button type='button' onclick=\"window.location='/index.php?page=" . ($_GET['page'] + 1) . "'\">→</button>";
}
else
{
	$res = $pdo->query("SELECT * FROM photos ORDER BY date DESC LIMIT " . (15) . ", 15;");
	if ($res->rowCount())
		echo "<button type='button' onclick=\"window.location='/index.php?page=2'\">→</button>";
}

echo ("</div>");
?>
