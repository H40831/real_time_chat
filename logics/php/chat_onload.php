<?php

require_once __DIR__."/db_access.php";
session_start();

ini_set('display_errors', 1);
error_reporting(-1);

$db = new MySql;

$initialInfo = [
	'user_id'=>$_SESSION['user_id'],
    'user_name'=>
    	$db->sql("SELECT user_name FROM users Where user_id = {$_SESSION['user_id']}")[0]['user_name'],
    'login_id'=>$_SESSION['login_id'],
    'current_room'=>isset($_SESSION['current_room']) ? $_SESSION['current_room'] : null,
    'current_room_name'=>isset($_SESSION['current_room']) ?
    	$db->sql("SELECT room_name FROM talk_rooms WHERE room_id = {$_SESSION['current_room']}")[0]['room_name']:
    	null,
];
$response = json_encode( $initialInfo );
echo $response;
