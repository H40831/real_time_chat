<?php

require_once __DIR__."/db_access.php";
session_start();

ini_set('display_errors', 1);
error_reporting(-1);

$adder_id = (int)filter_input(INPUT_POST,'adder_id');
$room_name = filter_input(INPUT_POST,'room_name');
$room_members = filter_input(INPUT_POST,'room_members');
$room_members = json_decode( $room_members );
$room_members = array_unique( $room_members );

$added_members = [];

$roomAdder = new MySql;

$roomAdder->sql(
	"INSERT talk_rooms VALUES (null, :room_name);",
	':room_name', $room_name
);
$added_room = (int)$roomAdder->lastInsertId();

//ルームを作成して、作成したルームのIDを取得するところまで出来た。
//次やること:
//room_membersをforEachでループさせて、それぞれのuser_idを取得。

if( $room_members !== [""] ){
    forEach( $room_members as $i ){
        $member_id = $roomAdder->sql( "SELECT user_id FROM users WHERE login_id = :i;",
            ":i", $i
        );
        if( empty($member_id[0]) ){
            trigger_error("ユーザーIDが {$i} の方を見つけられませんでした。");
        }else{
            $added_members[] = $member_id[0]['user_id'];
        }
    }
}

array_unshift($added_members, $adder_id);
$added_members = array_unique( $added_members );

forEach( $added_members as $i ){
    $roomAdder->sql( "INSERT talk_room_members VALUES({$i},{$added_room});" );
}


//取得したuser_idをtalk_room_membersに追加する。
//返り値をつかって、jsのほうでmove_roomsをチェインさせる。（作成した部屋に移動させる。）


echo ( json_encode($added_room) );

