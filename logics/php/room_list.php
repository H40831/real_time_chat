<?php

require_once __DIR__."/db_access.php";
session_start();

ini_set('display_errors', 1);
error_reporting(-1);

$listGetter = new MySQL;
$roomArray = $listGetter->sql(
	'SELECT talk_rooms.room_id,talk_rooms.room_name FROM talk_rooms JOIN talk_room_members ON talk_rooms.room_id = talk_room_members.room_id WHERE user_id = :user_id;',
	':user_id',$_SESSION['user_id']
);
$roomList = json_encode($roomArray);
echo $roomList;
