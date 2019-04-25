<?PHP
session_start();
require_once "config/database.php";

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Connection failed: Sorry !");
}

$likenb = 0;

if (is_numeric($_POST['pid']) && isset($_SESSION['uid']))
{
	if ($pdo->query("SELECT COUNT(*) FROM photos WHERE `pid` LIKE \"" . $_POST['pid'] . "\";")->fetch()[0])
	{
		$likenb = $pdo->query("SELECT COUNT(*) FROM likes WHERE `id_photo` LIKE \"" . $_POST['pid'] . "\";")->fetch()[0];
		try {
			$pdo->query("INSERT INTO likes (id_photo, id_user) VALUES (" . $_POST['pid']. "," . $_SESSION['uid'] . ");");
			echo json_encode(array('pid' => $_POST['pid'], 'state' => 1, 'newval' => $likenb + 1));
		} catch (PDOException $e) {
			$pdo->query("DELETE FROM likes WHERE id_photo LIKE " . $_POST['pid']. " AND id_user LIKE " . $_SESSION['uid'] . ";");
			echo json_encode(array('pid' => $_POST['pid'], 'state' => -1, 'newval' => $likenb - 1));
		}
	}
}

?>
