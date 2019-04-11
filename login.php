<?PHP
session_start();

if ($_SESSION['uname']) {
	if ($_SERVER['REQUEST_URI'] == "/login.php")
		header("Location: index.php");
	echo "<a style='margin: 8px;' href='/account.php'>My Account</a>";
	echo "<form id='login-form' action='/logout.php' method='post'>";
	echo "Signed in as " . $_SESSION['uname'] . "<br>" . $_SESSION['email'] . "<br>";
	echo "<input type='submit' name='submit' value='Logout'>";
	echo "</form>";
	return;
}

if ($_POST['submit'] == "Login" && $_POST['username'] && ctype_alpha($_POST['username']) && strlen($_POST['password']) >= 4) {
	require_once "config/database.php";

	try {
		$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		die("Connection failed: Sorry !");
	}

	$res = $pdo->query("SELECT * FROM userlist WHERE `username` LIKE \"" . $_POST['username'] . "\";");
	$err = true;
	foreach ($res as $ulog) {
		if ($ulog['username'] == $_POST['username']) {
			if (password_verify($_POST['password'], $ulog['password'])) {
				if ($ulog['mailconfirm'] != 0) {
					$errmail = true;
				} else {
					$err = false;
					$_SESSION['uname'] = $_POST['username'];
					$_SESSION['email'] = $ulog['email'];
				}
			}
		}
	}
}

if (isset($err) && !$err && $_SERVER['REQUEST_URI'] == "/login.php") {
	header("Location: index.php");
	exit;
}

require_once "header.php";
if ($err) {
	if (isset($errmail))
		echo "Please validate your email first<br>";
	else
		echo "Please Verify your credentials<br>";
}

?>

<form id="login-form" action="/login.php" method="post">
	Username<br>
	<input type="text" pattern="[A-Za-z]+" name="username" autofocus required><br>
	Password<br>
	<input type="password" minlength="4" name="password" required><br>
	<a href="/password_reset.php">Forgot password?</a><br>
	<input type="submit" name="submit" value="Login">
	<a href="/register.php">register</a>
</form>

