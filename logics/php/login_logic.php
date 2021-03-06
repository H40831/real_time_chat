<?php

require_once __DIR__."/db_access.php";
session_start();

ini_set('display_errors', 1);
error_reporting(-1);

# API処理の練習のために作成したが、本来はソーシャルログインやフレームワークを使用すべき。

# [テストアカウント1] id:onna  / pw:onna
# [テストアカウント2] id:otoko / pw:otoko

$inputId = filter_input(INPUT_POST,'id');
$inputPw = filter_input(INPUT_POST,'pw');
$signupFlag = filter_input(INPUT_POST,'loginOrSignup')==="signup"; 

class Authentication extends MySql {
	/**
	*$result
	*1:フォームに空欄がある
	*2:該当IDなし
	*3:ログイン成功
	*4:パスワード不一致
	*5:ID重複
	*6:登録完了
	*7:禁止文字が使用された
	*/
	private $result = 0;

	public function __construct($inputId,$inputPw) {
    	parent::__construct();
    	$this->inputId = $inputId;
    	$this->inputPw = $inputPw;
	}

	private function isInputEmpty(){
		if( empty($this->inputId) || empty($this->inputPw) ){
			$this->result = 1;#フォームに空欄がある
			return true;
		}
	}

	public function login() {
		if($this->isInputEmpty()){
			return;
		}

		$this->userInfo = $this->sql(
			'SELECT * FROM users WHERE login_id = :inputId;',
			':inputId',$this->inputId
		);
		if( empty($this->userInfo) ){
			$this->result = 2;#該当IDなし
			return;
		};

		if( password_verify($this->inputPw, $this->userInfo[0]['login_pw']) ){
	        $this->result = 3;#ログイン成功
	        $_SESSION['user_id']=$this->userInfo[0]['user_id'];
	        $_SESSION['user_name']=$this->userInfo[0]['user_name'];
	        $_SESSION['login_id']=$this->userInfo[0]['login_id'];
	    }else{
	    	$this->result = 4;#パスワード不一致
	    } 
	}

	public function signup() {
		if($this->isInputEmpty()){
			return;
		}
		if( strpos($this->inputId,',') !== false ){
    		$this->result = 7;
    		return;
    	}
		
		$this->userInfo = $this->sql(
			'SELECT * FROM users WHERE login_id = :inputId;',
			':inputId',$this->inputId
		);
		if( !empty($this->userInfo) ){ # inputIdに対するuserInfoが存在する場合、ID重複のためsignup()を中断する。
			$this->result = 5;#ID重複
			return;
		};

		$this->signupData = $this->sql(
			'INSERT users(login_id,login_pw) VALUES(:inputId,:inputPw);',
			':inputId',$this->inputId,
			':inputPw',password_hash($this->inputPw, PASSWORD_BCRYPT)
		);
		
		$this->result = 6;#登録完了

		$this->login();#正常であればここですぐにログイン処理へ移行するため、$this->result=6; が日の目を見ることは無い。
	}

	public function getResponse() {
		return json_encode( [$this->result] );
	}
}

$auth = new Authentication($inputId,$inputPw);

if( $signupFlag ) {
	$auth->signup();
	echo $auth->getResponse();
} else {
	$auth->login();
	echo $auth->getResponse();
}

