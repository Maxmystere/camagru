<?PHP
session_start();

function change_db($type, $email, $password, $newvalue)
{
	global $errpassword;
	require_once "config/database.php";

	try {
		$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		die("Connection failed: Sorry !");
	}
	$res = $pdo->query("SELECT * FROM userlist WHERE `email` LIKE \"" . $_POST['email'] . "\";");
	$userdata = $res->fetch();


	if ($type == 2) {
		$newpassword = hash("sha1", rand(1000, 156875322));
		$mailconfirm = hash("sha256", $newvalue . "Confirmation");
		$req = "UPDATE `userlist` SET `password` = \"" . password_hash($newpassword, PASSWORD_DEFAULT) . "\", `mailconfirm` = '" . $mailconfirm . "' WHERE `email` LIKE \"" . $_POST['email'] . "\";";
		$res = $pdo->query($req);
		mail($_POST['email'], "Camagru Register", "Click on this link to reset your password (new is " . $newpassword . " ) : " . $_SERVER['HTTP_HOST'] . "/mailconfirmator.php?u=" . $_SESSION['uname'] . "&c=" . $mailconfirm);
		unset($_SESSION['uname']);
		session_destroy();
		header("Location: logout.php");
		exit;

	} else if ($type == 3) {

		$res = $pdo->query("UPDATE `userlist` SET `password` = \"" . password_hash($newvalue, PASSWORD_DEFAULT) . "\" WHERE `email` LIKE \"" . $_POST['email'] . "\";");
		unset($_SESSION['uname']);
		session_destroy();
		header("Location: logout.php");
		exit;
	}

}

if ($_POST['submit'] == "Reset Password" && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	change_db(2, $_POST['email'], "Password", "NewPass");
}

require_once "header.php";

echo "Bah alors ? On oublie son mot de passe ?";
?>

<form id="login-form" action="/password_reset.php" method="post">
	Email:<br>
	<input type="email" name="email" required><br>
	<input type="submit" name="submit" value="Reset Password">
</form>
