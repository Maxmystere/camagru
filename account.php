<?php
session_start();

if (!$_SESSION['uname']) {
	header("Location: index.php");
	return;
}

$errpassword = false;

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

	if (!password_verify($password, $userdata['password'])) {
		$errpassword = true;
	} else {
		if ($type == 1) {
			$res = $pdo->query("UPDATE `userlist` SET `username` = \"" . $newvalue . "\" WHERE `email` LIKE \"" . $_POST['email'] . "\";");
			$_SESSION['uname'] = $newvalue;
			header("Location: account.php");
			exit;
		} else if ($type == 2) {

			$mailconfirm = hash("sha256", $newvalue . "Confirmation");
			$req = "UPDATE `userlist` SET `email` = '" . $newvalue . "', `mailconfirm` = '" . $mailconfirm . "' WHERE `email` LIKE \"" . $_POST['email'] . "\";";
			$res = $pdo->query($req);
			mail($newvalue, "Camagru Register", "Click on this link to validate your account : " . $_SERVER['HTTP_HOST'] . "/mailconfirmator.php?u=" . $_SESSION['uname'] . "&c=" . $mailconfirm);
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
}

if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && strlen($_POST['password']) >= 4) {
	if ($_POST['newusername'] != $_SESSION['uname'] && $_POST['submit'] == "Change Username" && $_POST['newusername'] && ctype_alpha($_POST['newusername'])) {
		//echo ("username change");
		change_db(1, $_POST['email'], $_POST['password'], $_POST['newusername']);
	} else if ($_POST['newemail'] != $_SESSION['email'] && $_POST['submit'] == "Change Email" && filter_var($_POST['newemail'], FILTER_VALIDATE_EMAIL)) {
		//echo ("mail change");
		change_db(2, $_POST['email'], $_POST['password'], $_POST['newemail']);
	} else if ($_POST['submit'] == "Change Password" && strlen($_POST['newpassword']) >= 4) {
		//echo ("password change");
		change_db(3, $_POST['email'], $_POST['password'], $_POST['newpassword']);
	}
}


require_once "config/database.php";
require_once "header.php";

echo "<div class='form-popup'>";
echo "<button type='button' onclick=\"document.getElementById('login-form').style.display = (document.getElementById('login-form').style.display == 'block' ? 'none' : 'block')\">+</button>";
require_once "login.php";
echo "</div>";

if ($errpassword)
	echo "ERROR ! Wrong Password";

?>

<div class='account-container'>
	<form action="/account.php" method="post">
	<input type="hidden" id="username1" name="email" value="<?PHP echo $_SESSION['email'] ?>">
		<table style="width:100%">
			<caption>Change Email</caption>
			<tr>
				<td>New Email :</td>
				<td><input type="email" name="newemail" required><br></td>
			</tr>
			<tr>
				<td>Password :</td>
				<td><input type="password" minlength="4" name="password" required></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" name="submit" value="Change Email"></td>
			</tr>
		</table>
	</form>
	<form action="/account.php" method="post">
	<input type="hidden" id="username2" name="email" value="<?PHP echo $_SESSION['email'] ?>">
		<table style="width:100%">
			<caption>Change Username</caption>
			<tr>
				<td>New Username :</td>
				<td><input type="text" pattern="[A-Za-z]+" name="newusername" autofocus required></td>
			</tr>
			<tr>
				<td>Password :</td>
				<td><input type="password" minlength="4" name="password" required></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" name="submit" value="Change Username"></td>
			</tr>
		</table>
	</form>
	<form action="/account.php" method="post">
	<input type="hidden" id="username3" name="email" value="<?PHP echo $_SESSION['email'] ?>">
		<table style="width:100%">
			<caption>Change Password</caption>
			<tr>
				<td>Old Password :</td>
				<td><input type="password" minlength="4" name="password" required></td>
			</tr>
			<tr>
				<td>New Password :</td>
				<td><input type="password" minlength="4" name="newpassword" required></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" name="submit" value="Change Password"></td>
			</tr>
		</table>
	</form>
</div>