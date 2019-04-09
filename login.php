<?PHP
session_start();

if ($_SESSION['uname'])
{
	if ($_SERVER['REQUEST_URI'] == "/login.php")
		header("Location: index.php");
	echo "<form id='login-form' action='/logout.php' method='post'>";
	echo "<input type='submit' name='submit' value='Logout'>";
	echo "</form>";
	exit;
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
			if ($ulog['password'] == $_POST['password'])
			{
				$err = false;
				$_SESSION['uname'] = $_POST['username'];
			}
			else
				$err = true;
		}
		else
			$err = true;
	}
}

if (!$err && $_SERVER['REQUEST_URI'] == "/login.php")
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
</form>
