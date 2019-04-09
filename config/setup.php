<?PHP

if ($_SERVER['REQUEST_URI'] == "/config/setup.php")
{
	header("Location: index.php");
	exit;
}

require_once "database.php";

try {
	$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    // set the PDO error mode to exception
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	echo "Connected successfully" . PHP_EOL;

	$sql_requests = array(
		"DROP TABLE userlist",
		//"DROP TABLE content",
		"CREATE TABLE `camagru`.`userlist`
		(
			`uid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`email` VARCHAR(255) NOT NULL,
			`username` VARCHAR(45) NOT NULL,
			`password` VARCHAR(255) NOT NULL,
			`creationtime` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			`mailconfirm` VARCHAR(255) NOT NULL,
			PRIMARY KEY (`uid`)
		);",
		"INSERT INTO userlist (email, username, password, mailconfirm) VALUE ('root@localhost', 'root', '". password_hash('root', PASSWORD_DEFAULT) ."', false)"
	);
	
	
	foreach ($sql_requests as $sql) {
		try {
			$res = $pdo->query($sql);
			echo (strtok($sql, "\n") . " Done" . PHP_EOL);
		} catch (PDOException $e) {
			echo ("ERROR: Could not able to execute $sql. "
				. $e->getMessage() . PHP_EOL);
		}
	}

} catch (PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}
