<?PHP
session_start();

if ($_SESSION['uname'])
{
	if ($_SERVER['REQUEST_URI'] == "/register.php")
		header("Location: index.php");
	exit;
}

if ($_POST['submit'] == "Register" && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && $_POST['username'] && ctype_alpha($_POST['username']) && strlen($_POST['password']) >= 4) {
	require_once "config/database.php";

	try {
		$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		die("Connection failed: Sorry !");
	}

	$req = "INSERT INTO userlist (email, username, password) VALUE (". $_POST['email'] . ", " . $_POST['username'] . ", " . password_hash($_POST['password'], PASSWORD_DEFAULT) . ")";

	$res = $pdo->query($req);


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

<form id="login-form" action="/register.php" method="post">
	Email:<br>
	<input type="email" name="email" required><br>
	Username:<br>
	<input type="text" pattern="[A-Za-z]+" name="username" required><br>
	Password:<br>
	<input type="password" minlength="4" name="password" required><br>
	<input type="submit" name="submit" value="Register">
</form>
