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
$add_member_flg = $add_members !== [""];

$adding_members = [];

if( $add_room_flg ){
    $room_name = filter_input(INPUT_POST,'room_name');
    $adder->sql(
        "INSERT talk_rooms VALUES (null, :room_name);",
        ':room_name', $room_name
    );
    $adding_room = (int)$roomAdder->lastInsertId();

    if( !$add_member_flg ){
        array_unshift($adding_members, $_SESSION['user_id']);
    }
}

forEach( $add_members as $i ){

    $member_id = $adder->sql( "SELECT user_id FROM users WHERE login_id = :i;",
        ":i", $i
    );
    if( empty($member_id[0]) ){
        $response = ["error"];
        $response[] = "ユーザーIDが {$i} の方を見つけられませんでした。";
        echo( json_encode($response) );
        exit;
    }else{
        $adding_members[] = $member_id[0]['user_id'];
    }
}

$adding_members = array_unique( $adding_members );

if( !$add_room_flg ){
    $adding_room = $_SESSION['current_room'];
}

forEach( $adding_members as $i ){
    try{
        $adder->sql( "INSERT talk_room_members VALUES({$i},{$adding_room});" );
    }catch (PDOException $err) {
        $response = ["error"];
        $err = $err->getMessage();
        if( strpos( $err,'SQLSTATE[23000]' )!==false ){
            $response[] = "既に存在するユーザーが指定されました。存在するユーザーのみ追加します。";
        }else{
            $response[] = $err;
        }
        echo( json_encode($response) );
        exit;
    }
}

if( $add_room_flg ){
    echo ( json_encode($adding_room) );
}else{
    echo ( json_encode($adding_members) );
}

