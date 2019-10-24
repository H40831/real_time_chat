<?php

ini_set('display_errors', 1);

require_once __DIR__."/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

try {
    $db = new PDO(
        'mysql:host=my-rds.c7rekwisb6xy.ap-northeast-1.rds.amazonaws.com;dbname=real_time_chat;charset=utf8mb4',
        $_ENV['DB_ID'],
        $_ENV['DB_PW'],
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        )
    );
} catch(PDOException $e) {
    $error = $e->getMessage();
}

$_['message']　= NULL;

$speech = function() {
	if( $_['message'] !== NULL ){
		$query = $db->prepare('INSERT talk_logs VALUE(NULL, ?, NOW(), $_POST['user_id'], $_POST['room_id'])');
		$query->bindParam(1, $_POST['message']);
		$query->execute();
		$_['message'] = NULL;
	}
};