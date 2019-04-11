<?php
session_start();

if ($_SESSION['uname'] != "root")
{
	header("Location: index.php");
	return;
}

require_once "config/database.php";
require_once "header.php";

echo "<div class='form-popup'>";
echo "<button type='button' onclick=\"document.getElementById('login-form').style.display = (document.getElementById('login-form').style.display == 'block' ? 'none' : 'block')\">+</button>";
require_once "login.php";
echo "</div>";


try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	echo "Connected successfully";
} catch (PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}

try {
	$sql = "SELECT * FROM userlist";
	$res = $pdo->query($sql);
	if ($res->rowCount() > 0) {
		echo "<table>";
		echo "<tr>";
		echo "<th>UID</th>";
		echo "<th>Email</th>";
		echo "<th>V</th>";
		echo "<th>Username</th>";
		echo "</tr>";
		while ($row = $res->fetch()) {
			echo "<tr>";
			echo "<td>" . $row['uid'] . "</td>";
			echo "<td>" . $row['email'] . "</td>";
			echo "<td>" . $row['mailconfirm'] . "</td>";
			echo "<td>" . $row['username'] . "</td>";
			echo "</tr>";
		}
		echo "</table>";
		unset($res);
	} else {
		echo "No matching records are found.";
	}
} catch (PDOException $e) {
	die("ERROR: Could not able to execute $sql. "
		. $e->getMessage());
}

$conn = null;

?>
