<?PHP
session_start();

if ($_SESSION['uname'])
{
	if ($_SERVER['REQUEST_URI'] == "/login.php")
		header("Location: index.php");
	echo "<form id='login-form' action='/logout.php' method='post'>";
	echo "Hello " . $_SESSION['uname'] . "<br>" . $_SESSION['email'] . "<br>";
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

	$res = $pdo->query("SELECT * FROM userlist");

	foreach ($res as $ulog) {
		if ($ulog['username'] == $_POST['username']) {
			if (password_verify($_POST['password'], $ulog['password']))
			{
				$err = false;
				$_SESSION['uname'] = $_POST['username'];
				$_SESSION['email'] = $ulog['email'];
			}
			else if (!isset($err))
				$err = true;
		}
		else if (!isset($err))
			$err = true;
	}
}

if (isset($err) && !$err && $_SERVER['REQUEST_URI'] == "/login.php")
{
	header("Location: index.php");
	exit;
}

require_once "header.php";
if ($err)
{
	echo "Please Verify your credentials<br><br>";
}

?>

<form id="login-form" action="/login.php" method="post">
  Username:<br>
  <input type="text" pattern="[A-Za-z]+" name="username" autofocus required><br>
  Password:<br>
  <input type="password" minlength="4" name="password" required><br>
  <input type="submit" name="submit" value="Login">
  <a href="/register.php">Or register</a>
</form>

