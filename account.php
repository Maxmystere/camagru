<?php
session_start();

if (!$_SESSION['uname']) {
	header("Location: index.php");
	return;
}

require_once "config/database.php";
require_once "header.php";

echo "<div class='form-popup'>";
echo "<button type='button' onclick=\"document.getElementById('login-form').style.display = (document.getElementById('login-form').style.display == 'block' ? 'none' : 'block')\">+</button>";
require_once "login.php";
echo "</div>";

echo "Hello " . $_SESSION['uname'] . "<br>" . $_SESSION['email'] . "<br>";

?>

<div class='account-container'>
	<form action="/email.php" method="post">
		<table style="width:100%">
			<caption>Change Email</caption>
			<tr>
				<td>New Email :</td>
				<td><input type="email" name="email" required><br></td>
			</tr>
			<tr>
				<td>Password :</td>
				<td><input type="password" minlength="4" name="password" required></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" name="submit" value="Update Username"></td>
			</tr>
		</table>
	</form>
	<form action="/username.php" method="post">
		<table style="width:100%">
			<caption>Change Username</caption>
			<tr>
				<td>New Username :</td>
				<td><input type="text" pattern="[A-Za-z]+" name="username" autofocus required></td>
			</tr>
			<tr>
				<td>Password :</td>
				<td><input type="password" minlength="4" name="password" required></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" name="submit" value="Update Username"></td>
			</tr>
		</table>
	</form>
	<form action="/password.php" method="post">
		<table style="width:100%">
			<caption>Change Password</caption>
			<tr>
				<td>Old Password :</td>
				<td><input type="password" minlength="4" name="password" required></td>
			</tr>
			<tr>
				<td>New Password :</td>
				<td><input type="password" minlength="4" name="password" required></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" name="submit" value="Change Password"></td>
			</tr>
		</table>
	</form>
</div>
