<?PHP
session_start();
require_once "config/database.php";

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Connection failed: Sorry !");
}

if (is_numeric($_POST['pid']) && isset($_SESSION['uid']))
{
	if ($img = $pdo->query("SELECT * FROM photos WHERE `pid` LIKE \"" . $_POST['pid'] . "\" AND `id_user` LIKE \"" . $_SESSION['uid'] . "\";")->fetch())
	{
		$pdo->query("DELETE FROM likes WHERE id_photo LIKE " . $_POST['pid'] . ";");
		$pdo->query("DELETE FROM photos WHERE pid LIKE " . $_POST['pid']. " AND id_user LIKE " . $_SESSION['uid'] . ";");
		header("Location: index.php");
		echo "Success Delete\n";
	}
}

?>
