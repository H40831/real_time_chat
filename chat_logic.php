<?php

require_once __DIR__."/db_access.php";
session_start();

ini_set('display_errors', 1);
error_reporting(-1);

$inputMessage = filter_input(INPUT_POST,'message');
$inputName = filter_input(INPUT_POST,'name');

class ChatController extends MySql {
	private $response = [
		"user_name" => "",
	];

	public function __construct($inputMessage,$inputName) {
    	parent::__construct();
    	$this->inputMessage = $inputMessage;
    	$this->inputName = $inputName;
    	$this->userInfo = $this->sql(
			'SELECT * FROM users WHERE user_id = :user_id;',
			':user_id',$_SESSION['user_id']
		);
	}
	public function setUserName() {
		//HTMLの更新は不要 //理由:HTMLの入力値をDBに登録しているだけなので
		//データベースの更新
		$this->sql(
			'UPDATE users SET user_name = :inputName WHERE user_id = :user_id',
			':inputName',$this->inputName,
			':user_id',$_SESSION['user_id']
		);
		//セッション変数の更新
		$_SESSION['user_name'] = $this->inputName;
	}
	public function sendMessage() {
		$talk_id = null;
		$talk_value = $this->inputMessage;
		$talk_time = date("Y-m-d H:i:s");
		$user_id = $_SESSION['user_id'];
		$room_id = 1;
		$this->sql(
			'INSERT talk_logs VALUE(:talk_id, :talk_value, :talk_time, :user_id, :room_id);',
			':talk_id',$talk_id,
			':talk_value',$talk_value,
			':talk_time',$talk_time,
			':user_id',$user_id,
			':room_id',$room_id
		);
	}

	public function getResponse() {
		return json_encode( [$this->response] );
	}
}

$ctrl = new ChatController($inputMessage,$inputName);
$ctrl->setUserName();
$ctrl->sendMessage();
$ctrl->getResponse();
