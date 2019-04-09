<?php
/*
function myprint_r($my_array)
{
	if (is_array($my_array)) {
		echo "<table border=1 cellspacing=0 cellpadding=3 width=100%>";
		echo '<tr><td colspan=2 style="background-color:#333333;"><strong><font color=white>ARRAY</font></strong></td></tr>';
		foreach ($my_array as $k => $v) {
			echo '<tr><td valign="top" style="width:40px;background-color:#F0F0F0;">';
			echo '<strong>' . $k . "</strong></td><td>";
			myprint_r($v);
			echo "</td></tr>";
		}
		echo "</table>";
		return;
	}
	echo $my_array;
}

myprint_r($_GET);
*/
$errtoken = true;
$erruser = true;
if ($_GET['u'] && ctype_alpha($_GET['u']) && $_GET['c']) {
	require_once "config/database.php";

	try {
		$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		die("Connection failed: Sorry !");
	}

	$res = $pdo->query("SELECT * FROM userlist WHERE `username` LIKE \"" . $_GET['u'] . "\";");
	$errtoken = false;
	$erruser = false;
	foreach ($res as $ulog) {
		if ($ulog['username'] == $_GET['u']) {
			if ($_GET['c'] == $ulog['mailconfirm']) {
				echo ("YOUPI Your email is confirmed");
				$res = $pdo->query("UPDATE userlist SET `mailconfirm` = 0 WHERE `uid` LIKE \"" . $ulog['uid'] . "\";");
			} else
				$errtoken = true;
		} else
			$erruser = true;
	}
}

if ($errtoken) {
	echo "Token Error<br><br>";
}
if ($erruser) {
	echo "Username error<br><br>";
}

?>
