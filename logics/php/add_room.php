<?php

require_once __DIR__."/db_access.php";
session_start();

ini_set('display_errors', 1);
error_reporting(-1);

$add_members = filter_input(INPUT_POST,'add_members');
$add_members = json_decode( $add_members );
$add_members = array_unique( $add_members );

$adder = new MySql;
$add_room_flg = !empty( filter_input(INPUT_POST,'room_name') );

$adding_members = [];
$response = [];

if( $add_room_flg ){
    $room_name = filter_input(INPUT_POST,'room_name');
    $adder->sql(
        "INSERT talk_rooms VALUES (null, :room_name);",
        ':room_name', $room_name
    );
    $adding_room = (int)$adder->lastInsertId();
    $adding_members = [$_SESSION['user_id']];
}

forEach( $add_members as $i ){

    $member_id = $adder->sql( "SELECT user_id FROM users WHERE login_id = :i;",
        ":i", $i
    );
    if( empty($member_id[0]) ){
        array_unshift($response,"error");
        $response[] = "ユーザーIDが {$i} の方を見つけられませんでした。存在するユーザーのみ追加します。";
    }else{
        $adding_members[] = $member_id[0]['user_id'];
    }
}

$adding_members = array_unique( $adding_members );

if( !$add_room_flg ){
    $adding_room = $_SESSION['current_room'];
}

$added_members=[];
forEach( $adding_members as $i ){
    try{
        $adder->sql( "INSERT talk_room_members VALUES({$i},{$adding_room});" );
        $added_members[]=$i;
    }catch (PDOException $err) {
        if( strpos( $err,'SQLSTATE[23000]' )===false ){
            //SQLSTATE[23000]は、複合ユニークキー違反のよう。既に追加されてるユーザーが追加されなくても問題ないため、エラーから除外する。
            header('HTTP/1.1 400 Bad Request');
            array_unshift($response,"error");
            $response[] = $err->getMessage();
        }
    }
}

if( $add_room_flg ){
    $response[] = $adding_room;
}else{
    $response[] = $added_members;
}

echo json_encode($response);