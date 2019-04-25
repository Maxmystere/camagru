<?PHP
session_start();
require_once "config/database.php";

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Connection failed: Sorry !");
}

if (is_numeric($_POST['pid']) && isset($_SESSION['uid']) && $_POST['comment'])
{
	if ($img = $pdo->query("SELECT * FROM photos WHERE `pid` LIKE \"" . $_POST['pid'] . "\";")->fetch())
	{
		if ($user = $pdo->query("SELECT * FROM userlist WHERE `mailnotif` LIKE 1 AND `uid` LIKE \"" . $img['id_user'] . "\";")->fetch())
		{
			mail($user['email'], "You have a new comment !", "Image URL : " . $_SERVER['HTTP_HOST'] . "/photo.php?pid=" . $_POST['pid'] . "\n" . $_POST['comment']);
		}
		$pdo->query("INSERT INTO comments (id_photo, id_user, comment) VALUES (" . $_POST['pid']. "," . $_SESSION['uid'] . ", '". base64_encode($_POST['comment']) ."' );");
		echo json_encode(array('pid' => $_POST['pid'], 'newcom' => base64_encode($_POST['comment']), 'u' => $user, 'im' => $img));
	}
}

?>
