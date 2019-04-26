<?PHP
session_start();
require_once "config/database.php";

if (!is_numeric($_GET['pid'])) {
	header("Location: index.php");
	exit;
}

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Connection failed: Sorry !");
}

if (!($data = $pdo->query("SELECT * FROM photos WHERE `pid` LIKE \"" . $_GET['pid'] . "\";")->fetch()))
{
	header("Location: index.php");
	exit;
}

require_once "header.php";

echo "<div class='form-popup'>";
echo "<button type='button' onclick=\"document.getElementById('login-form').style.display = (document.getElementById('login-form').style.display == 'block' ? 'none' : 'block')\">+</button>";
require_once "login.php";
echo "</div>";

$user = $pdo->query("SELECT (`username`) FROM userlist WHERE `uid` LIKE \"" . $data['id_user'] . "\";")->fetch();
$likes = $pdo->query("SELECT COUNT(*) FROM likes WHERE `id_photo` LIKE \"" . $data['pid'] . "\";")->fetch()[0];
echo ("<div class='solo-container'>");
echo "<div>";
echo "<img src='data:image/png;base64, " . ($data['photo']) . "' alt='Img'/>";
echo "<div id='liketxt" . $data['pid'] . "' style='width: 100%;float: left;top: 10px;' class='liketxt'>" . $likes . " ♥</div>";
echo "<div style='width: 30%;float: right;'>by " . $user['username'] . " " . "</div>";
if (isset($_SESSION['uid'])) {
	if ($data['id_user'] == $_SESSION['uid'])
	{
		echo "<form style='float: left;' method='POST' action='/deletor.php'>";
		echo "<input type='hidden' name='pid' value='" . $data['pid'] . "' />";
		echo " <input type='submit' id='delbtn' class='likebtn' name='UP' value='🗑' style='background-color: white;border-radius: 40%;' />";
		echo "</form>";
	}
	echo "<form style='width: 30%;float: left;' class='likeform'>";
	echo "<input type='hidden' name='pid' value='" . $data['pid'] . "' />";
	if ($pdo->query("SELECT COUNT(*) FROM likes WHERE `id_photo` LIKE \"" . $data['pid'] . "\" AND id_user LIKE \"" . $_SESSION['uid'] . "\";")->fetch()[0])
		echo " <input type='submit' id='likebtn" . $data['pid'] . "' class='likebtn' name='UP' value='♥' style='background-color: red;border-radius: 50%;' />";
	else
		echo " <input type='submit' id='likebtn" . $data['pid'] . "' class='likebtn' name='UP' value='♥' style='background-color: white;' />";
	echo "</form>";
}
echo "</div>";
echo "<div class='commdiv'>";
if (isset($_SESSION['uid'])) {
	echo "<form class='commform' method='POST'>";
	echo "<input type='hidden' name='pid' value='" . $data['pid'] . "' />";
	echo "<input class='commenttext' type='text' name=comment>";
	echo "<input type='submit' name='submit' value='Post'>";
	echo "</form>";
}
$comments = $pdo->query("SELECT * FROM comments WHERE `id_photo` LIKE \"" . $data['pid'] . "\" ORDER BY date DESC;");
foreach ($comments as $comment)
{
	$user = $pdo->query("SELECT (`username`) FROM userlist WHERE `uid` LIKE \"" . $comment['id_user'] . "\";")->fetch()['username'];
	//print_r($user);
	echo "<p>";
	echo "<b>" . $user . "</b>";
	echo " : " . base64_decode($comment['comment']);
	echo "</p>";
}
echo ("</div>");
echo ("</div>");
?>
