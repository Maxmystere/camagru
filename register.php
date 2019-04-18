<?PHP
session_start();

if (isset($_SESSION['uname'])) {
	header("Location: index.php");
	exit;
}

$errmail = false;
$errusername = false;

if ($_POST['submit'] == "Register" && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && $_POST['username'] && ctype_alpha($_POST['username']) && strlen($_POST['password']) >= 4) {
	require_once "config/database.php";

	try {
		$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		die("Connection failed: Sorry !");
	}

	$res = $pdo->query("SELECT * FROM userlist WHERE `username` LIKE \"" . $_POST['username'] . "\" OR `email` LIKE \"" . $_POST['email'] . "\";");
	foreach ($res as $ulog) {
		if ($ulog['email'] == $_POST['email'])
			$errmail = true;
		if ($ulog['username'] == $_POST['username'])
			$errusername = true;
	}
	if ($errmail == false && $errusername == false) {
		$mailconfirm = hash("sha256", $_POST['username'] . "Confirmation");
		$req = "INSERT INTO userlist (email, username, password, mailconfirm) VALUE ('" . $_POST['email'] . "', '" . $_POST['username'] . "', '" . password_hash($_POST['password'], PASSWORD_DEFAULT) . "', '". $mailconfirm . "')";
		$res = $pdo->query($req);
		mail($_POST['email'], "Camagru Register", "Click on this link to validate your account : " . $_SERVER['HTTP_HOST'] . "/mailconfirmator.php?u=" . $_POST['username'] . "&c=" . $mailconfirm);
		header("Location: login.php?usercreation=complete");
		exit;
	}

}


require_once "header.php";
if ($errmail) {
	echo "Email already used<br><br>";
}
if ($errusername) {
	echo "Username already used<br><br>";
}

?>

<form id="login-form" action="/register.php" method="post">
	Email:<br>
	<input type="email" name="email" required><br>
	Username:<br>
	<input type="text" pattern="[A-Za-z]+" name="username" required><br>
	Password:<br>
	<input type="password" minlength="4" name="password" required><br><br>
	<input type="submit" name="submit" value="Register">
</form>

