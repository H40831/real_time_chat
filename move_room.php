<?php

require_once __DIR__."/db_access.php";
session_start();

ini_set('display_errors', 1);
error_reporting(-1);

$room_id = filter_input(INPUT_POST,'room_id');

$roomInfoGetter = new MySql;
$isJoiningRoom = $roomInfoGetter->sql(
	"SELECT COUNT(*) FROM talk_rooms JOIN talk_room_members ON talk_rooms.room_id = talk_room_members.room_id WHERE user_id = :user_id AND talk_rooms.room_id = :room_id;",
	':room_id',$room_id,
	':user_id',$_SESSION['user_id']
)[0]['COUNT(*)'];

if( $isJoiningRoom ){
	$_SESSION['current_room'] = $room_id;

	$logs = $roomInfoGetter->sql(
		"SELECT DISTINCT talk_id,talk_value,talk_time,user_name,users.user_id FROM talk_logs 
		JOIN talk_room_members ON talk_logs.room_id = talk_room_members.room_id 
		JOIN users ON talk_logs.user_id = users.user_id 
		WHERE talk_logs.room_id = {$room_id} 
		ORDER BY talk_time;"
	);
	
	$result = [];
	foreach($logs as $log) {
		$talk_value = htmlspecialchars( $log['talk_value'] );
		$result[] = [
			'talk_id' => $log['talk_id'],
			'talk_time' => $log['talk_time'],
			'talk_value' => nl2br( $talk_value ),
			'user_id' => $log['user_id'],
			'user_name' => htmlspecialchars( $log['user_name'] ),
		];
	}
	echo ( json_encode($result) );
}else{
	error_log( "Unexpected behavior: user_id_{$_SESSION['user_id']} is trying access to not joined room. (tryed connect to room_id_{$room_id}.)" );
}


