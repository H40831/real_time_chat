<?php

require_once __DIR__."/db_access.php";
session_start();

ini_set('display_errors', 1);
error_reporting(-1);

$room_name = filter_input(INPUT_POST,'room_name');
$room_members = filter_input(INPUT_POST,'room_members');
$room_members = json_decode( $room_members );
$added_members = [];

$roomAdder = new MySql;
/*
$roomAdder->sql(
	"INSERT talk_rooms VALUES (null, :room_name);",
	':room_name', $room_name
);

$added_room = $roomAdder->lastInsertId();
 */
//ルームを作成して、作成したルームのIDを取得するところまで出来た。
//次やること:
//room_membersをforEachでループさせて、それぞれのuser_idを取得。
forEach( $room_members as $i ){
    $isExist = $roomAdder->sql( "SELECT user_id FROM users WHERE login_id = :i;",
        ":i", $i
    );
    if( empty($isExist) ){
        trigger_error("ユーザーIDが {$i} の方を見つけられませんでした。");
    }else{
        $added_members[] = $i;
    }
}
/*動作未確認
forEach( $added_members as $i ){
    $roomAdder->sql( "INSER talk_room_members VALUES({$added_id},{$i});" );
}
 */


//取得したuser_idをtalk_room_membersに追加する。
//返り値をつかって、jsのほうでmove_roomsをチェインさせる。（作成した部屋に移動させる。）


echo ( json_encode([$added_room]) );

