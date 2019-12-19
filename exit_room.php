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
    echo( json_encode([$user,$room]));
    exit;
}catch (PDOException $err) {
    $response = ["error"];
    $err = $err->getMessage();
    //if( strpos( $err,'SQLSTATE[エラーNo]' )!==false ){
    //    $response[] = "エラーメッセージ";
    //}else{
        $response[] = $err;
    //}
    echo( json_encode($response) );
    exit;
}
