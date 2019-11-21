<?php

require_once __DIR__."/db_access.php";
session_start();

ini_set('display_errors', 1);
error_reporting(-1);

$initialInfo = [
	'user_id'=>$_SESSION['user_id'],
    'user_name'=>$_SESSION['user_name'],
    'login_id'=>$_SESSION['login_id'],
];
$response = json_encode( $initialInfo );
echo $response;
