<?php
session_start();

if (!$_SESSION['uname']) {
	header("Location: index.php");
	return;
}

$err = false;
$erremail = false;
$errusername = false;
$errpassword = false;
$passmatch = false;

/*
//	If type == 1, newvalue = newusername
//	If type == 2, newvalue = newemail
//	If type == 3, newvalue = newpassword
 */
function change_db($type, $email, $password, $newvalue)
{
	global $err;
	global $erremail;
	global $errusername;
	global $errpassword;
	global $passmatch;
	require_once "config/database.php";

	try {
		$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		die("Connection failed: Sorry !");
	}
	$res = $pdo->query("SELECT * FROM userlist WHERE `email` LIKE \"" . $email . "\";");
	$userdata = $res->fetch();

	if (!password_verify($password, $userdata['password'])) {
		$errpassword = true;
	} else {
		if ($type == 1) { // If type == 1, newvalue = newusername
			$checkres = $pdo->query("SELECT * FROM userlist WHERE `username` LIKE \"" . $newvalue . "\";");
			foreach ($checkres as $ulog) {
				if ($ulog['username'] == $newvalue)
					$errusername = true;
			}
			if ($errusername == false) {
				$res = $pdo->query("UPDATE `userlist` SET `username` = \"" . $newvalue . "\" WHERE `email` LIKE \"" . $email . "\";");
				$_SESSION['uname'] = $newvalue;
				header("Location: account.php");
				exit;
			}
		} else if ($type == 2) { // If type == 2, newvalue = newemail
			$checkres = $pdo->query("SELECT * FROM userlist WHERE `email` LIKE \"" . $newvalue . "\";");
			foreach ($checkres as $ulog) {
				if ($ulog['email'] == $newvalue)
					$erremail = true;
			}
			if ($erremail == false) {
				$mailconfirm = hash("sha256", $newvalue . "Confirmation");
				$req = "UPDATE `userlist` SET `email` = '" . $newvalue . "', `mailconfirm` = '" . $mailconfirm . "' WHERE `email` LIKE \"" . $email . "\";";
				$res = $pdo->query($req);
				mail($newvalue, "Camagru Register", "Click on this link to modify your email account : " . $_SERVER['HTTP_HOST'] . "/mailconfirmator.php?u=" . $_SESSION['uname'] . "&c=" . $mailconfirm);
				unset($_SESSION['uname']);
				session_destroy();
				header("Location: logout.php");
				exit;
			}
		} else if ($type == 3) { // If type == 3, newvalue = newpassword

			$res = $pdo->query("UPDATE `userlist` SET `password` = \"" . password_hash($newvalue, PASSWORD_DEFAULT) . "\" WHERE `email` LIKE \"" . $email . "\";");
			unset($_SESSION['uname']);
			session_destroy();
			header("Location: logout.php");
			exit;
		} else if ($type == 4) { // If type == 4, newvalue = confirmpassword, deleteaccount
			if ($password == $newvalue) {
				$pdo->query("DELETE FROM `comments` WHERE `id_user` LIKE \"" . $_SESSION['uid'] . "\";");
				$pdo->query("DELETE FROM `likes` WHERE `id_user` LIKE \"" . $_SESSION['uid'] . "\";");
				$pdo->query("DELETE FROM `photos` WHERE `id_user` LIKE \"" . $_SESSION['uid'] . "\";");
				$pdo->query("DELETE FROM `userlist` WHERE `email` LIKE \"" . $email . "\";");
				unset($_SESSION['uname']);
				session_destroy();
				header("Location: logout.php");
				exit;
			} else
				$passmatch = true;
		}
	}
}

function update_notif($email, $mailnotif)
{
	require_once "config/database.php";

	try {
		$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		die("Connection failed: Sorry !");
	}
	if (isset($mailnotif)) {
		$res = $pdo->query("UPDATE `userlist` SET `mailnotif` = true WHERE `email` LIKE \"" . $email . "\";");
		$_SESSION['mailnotif'] = 1;
	} else {
		$res = $pdo->query("UPDATE `userlist` SET `mailnotif` = false WHERE `email` LIKE \"" . $email . "\";");
		$_SESSION['mailnotif'] = 0;
	}
}

if (filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL) && strlen($_POST['password']) >= 4) {
	if ($_POST['newusername'] != $_SESSION['uname'] && $_POST['submit'] == "Change Username" && $_POST['newusername'] && ctype_alpha($_POST['newusername'])) {
		change_db(1, $_SESSION['email'], $_POST['password'], $_POST['newusername']);
	} else if ($_POST['newemail'] != $_SESSION['email'] && $_POST['submit'] == "Change Email" && filter_var($_POST['newemail'], FILTER_VALIDATE_EMAIL)) {
		change_db(2, $_SESSION['email'], $_POST['password'], $_POST['newemail']);
	} else if ($_POST['submit'] == "Change Password" && strlen($_POST['newpassword']) >= 4) {
		change_db(3, $_SESSION['email'], $_POST['password'], $_POST['newpassword']);
	} else if ($_POST['submit'] == "Delete Account" && strlen($_POST['newpassword']) >= 4) {
		change_db(4, $_SESSION['email'], $_POST['password'], $_POST['newpassword']);
	} else
		$err = true;
} else if ($_POST['submit'] == "Update Notification") {
	update_notif($_SESSION['email'], $_POST['mailnotif']);
}



require_once "config/database.php";
require_once "header.php";

echo "<div class='form-popup'>";
echo "<button type='button' onclick=\"document.getElementById('login-form').style.display = (document.getElementById('login-form').style.display == 'block' ? 'none' : 'block')\">+</button>";
require_once "login.php";
echo "</div>";

if ($err)
	echo "Something went wrong :(<br>";
if ($erremail)
	echo "Email already used<br>";
if ($errusername)
	echo "Username already used<br>";
if ($errpassword)
	echo "Wrong Password<br>";
if ($passmatch)
	echo "Passwords do not match<br>";
?>

<div class='account-container'>
	<form action="/account.php" method="post">
		<table style="width:100%">
			<caption>Notifications</caption>
			<tr>
				<td>Mail Notification :</td>
				<td><input type="checkbox" name="mailnotif" <?PHP if ($_SESSION['mailnotif']) { echo "checked"; } ?>></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" name="submit" value="Update Notification"></td>
			</tr>
		</table>
	</form>
	<form action="/account.php" method="post">
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
	<form action="/account.php" method="post">
		<table style="width:100%">
			<caption>Delete Account</caption>
			<tr>
				<td>Password :</td>
				<td><input type="password" minlength="4" name="password" required></td>
			</tr>
			<tr>
				<td>Confirm Password :</td>
				<td><input type="password" minlength="4" name="newpassword" required></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" name="submit" value="Delete Account"></td>
			</tr>
		</table>
	</form>
</div>
