<?php

ini_set('display_errors', 1);
error_reporting(-1);

require_once __DIR__."/db_access.php";

# API処理の練習のために作成したが、本来はソーシャルログインやフレームワークを使用すべき。

# [テストアカウント1] id:onna  / pw:onna
# [テストアカウント2] id:otoko / pw:otoko

$inputId = filter_input(INPUT_POST,'id');
$inputPw = filter_input(INPUT_POST,'pw');
$signupFlag = filter_input(INPUT_POST,'loginOrSignup')==="signup"; 

class Authentication extends MySql {
	private $result = [];

	public function __construct($inputId,$inputPw) {
    	parent::__construct();
    	$this->inputId = $inputId;
    	$this->inputPw = $inputPw;
	}

	private function isInputEmpty(){
		if( empty($this->inputId) || empty($this->inputPw) ){
			$this->result[] = '空欄があります';
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
			$this->result[] = '一致するIDが存在しません';
			return;
		};

		if( password_verify($this->inputPw, $this->userInfo[0]['login_pw']) ){
	        #ログイン成功 #成功時の処理書く
	        $this->result[] = 'ログインしました';

	    }else{
	    	$this->result[] = 'パスワードが一致しません';
	    } 
	}

	public function signup() {
		if($this->isInputEmpty()){
			return;
		}
		
		$this->userInfo = $this->sql(
			'SELECT * FROM users WHERE login_id = :inputId;',
			':inputId',$this->inputId
		);
		if( !empty($this->userInfo) ){ # inputIdに対するuserInfoが存在する場合、ID重複のためsignup()を中断する。
			$this->result[] = 'IDが重複してます';
			return;
		};

		$this->signupData = $this->sql(
			'INSERT users(login_id,login_pw) VALUES(:inputId,:inputPw);',
			':inputId',$this->inputId,
			':inputPw',password_hash($this->inputPw, PASSWORD_BCRYPT)
		);
		
		$this->result[] = '新規登録しました';

		$this->login();
	}

	public function getResponse() {
		return json_encode( $this->result );
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

