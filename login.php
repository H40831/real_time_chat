<?php

ini_set('display_errors', 1);
error_reporting(-1);

require_once __DIR__."/db_access.php";

$inputId = filter_input(INPUT_POST,'id');
$inputPw = filter_input(INPUT_POST,'pw');
$signupFlag = filter_input(INPUT_POST,'loginOrSignup')==="signup"; 

class Authentication extends MySql {
	private $data = [];

	public function __construct($inputId,$inputPw) {
    	parent::__construct();
    	$this->inputId = $inputId;
    	$this->inputPw = $inputPw;
	}

	public function login() {
		$this->userInfo = $this->sql(
			'SELECT * FROM users WHERE login_id = :inputId;',
			':inputId',$this->inputId
		);

		if( password_verify($inputPw, $this->userInfo['login_pw']) ){
	        #ログイン成功 #成功時の処理書く
	        $this->data['pattern1'] = 'ログインしました';

	    }else{
	    	$this->data['pattern2'] = 'パスワードが一致しません';
	    } 
	}

	public function signup() {
		$this->signupData = $this->sql(
			'INSERT users(login_id,login_pw) VALUES(:inputId,:inputPw);',
			':inputId',$this->inputId,
			':inputPw',$this->inputPw
		); #このままだとIDが重複していた際に、どのようなエラー文が返ってくるのか不明なので怖い。
		
		$this->data['pattern3'] = '新規登録しました';

		login();
	}

	public function getData() {
		return json_encode( $data );
	}
}

$auth = new Authentication($inputId,$inputPw);

if( $signupFlag ) {
	$auth->signup();
	echo $auth->getData();
} else {
	$auth->login();
	echo $auth->getData();
}

