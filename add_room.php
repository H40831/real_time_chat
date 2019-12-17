<?php

require_once __DIR__."/db_access.php";
session_start();

ini_set('display_errors', 1);
error_reporting(-1);

$room_name = filter_input(INPUT_POST,'room_name');
$room_members = filter_input(INPUT_POST,'room_members');
$room_name = json_decode( $room_name );

$roomAdder = new MySql;

$roomAdder->sql(
	"INSERT talk_rooms VALUES (null, :room_name);",
	':room_name', $room_name
);

$added_id = $roomAdder->lastInsertId();

//ルームを作成して、作成したルームのIDを取得するところまで出来た。
//次やること:
//room_membersをforEachでループさせて、それぞれのuser_idを取得。
//取得したuser_idをtalk_room_membersに追加する。
//返り値をつかって、jsのほうでmove_roomsをチェインさせる。（作成した部屋に移動させる。）




echo ( json_encode($result) );

