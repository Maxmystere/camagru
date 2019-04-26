<?PHP
session_start();
require_once "config/database.php";

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Connection failed: Sorry !");
}

if (isset($_POST['cam']) && is_numeric($_POST['mid']) && isset($_SESSION['uid'])) {
	print_r($_POST);
	$data = base64_decode($pdo->query("SELECT photo FROM meme WHERE `mid` LIKE \"" . $_POST['mid'] . "\";")->fetch()['photo']);

	$camsq = explode(";base64,", $_POST['cam'])[1];

	$img_meme = imagecreatefromstring($data);
	if (!($img_cam = imagecreatefromstring(base64_decode($camsq))))
		exit;
	imagesavealpha($img_meme, true);
	$color = imagecolorallocatealpha($img_meme, 0, 0, 0, 127);
	imagefill($img_meme, 0, 0, $color);
	$img_cam = imagescale($img_cam, 640, 480);
	$img_meme = imagescale($img_meme, 640, 480);
	
	imagecopy($img_cam, $img_meme, 0, 0, 0, 0, 640, 480);
	ob_start();
	imagepng($img_cam);
	$content = ob_get_contents();
	ob_end_clean();
	$pdo->query("INSERT INTO photos (`photo`, `id_user`) VALUES (\"" . base64_encode($content) . "\"," . $_SESSION['uid'] . ");");
	imagedestroy($img_cam);
	imagedestroy($img_meme);
}
?>
