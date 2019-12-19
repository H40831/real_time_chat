<?php

require_once __DIR__."/db_access.php";
session_start();

ini_set('display_errors', 1);
error_reporting(-1);

$user = $_SESSION['user_id'];
$room = filter_input(INPUT_POST,'room_id');

$remover = new MySql;
try{
    $remover->sql( "DELETE FROM talk_room_members WHERE room_id = :room AND user_id = :user;",
    ':room',$room,
    ':user',$user );

    //部屋に誰もいなくなった場合、その部屋を削除する。
    $isEmptyRoom = $remover->sql(
        "SELECT count(*) FROM talk_rooms
        LEFT JOIN talk_room_members ON talk_rooms.room_id = talk_room_members.room_id
        WHERE talk_rooms.room_id = {$room} AND talk_room_members.room_id IS null;"
    )[0]["count(*)"]===1;

    if( $isEmptyRoom ){
        $remover->sql(
            "DELETE FROM talk_logs WHERE room_id = {$room};"
        );
        $remover->sql(
            "DELETE FROM talk_rooms WHERE room_id = {$room};"
        );
    }

    echo( json_encode([$user,$room]));
    exit;
}catch (PDOException $err) {
    $response = ["error"];
    $err = $err->getMessage();
    $response[] = $err;
    echo( json_encode($response) );
    exit;
}
