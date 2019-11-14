<?php

ini_set('display_errors', 1);
error_reporting(-1);

require_once __DIR__."/db_access.php";

class Authentication extends MySql {
	private $inputId = $_POST['id'];
	private $inputPw = $_POST['pw'];
	private $signupFlag = $_POST['loginOrSignup']==="signup"; 
	private $response = [];

	public function __construct() {
    	parent::__construct();
    	
	}

	public function login() {
		$this->userInfo = $this->sql(
			'SELECT * FROM users WHERE login_id = :inputId;',
			':inputId',$this->inputId
		);

		if( password_verify($this->inputPw, $this->userInfo['login_pw']) ){
	        #ログイン成功 #成功時の処理書く
	        $this->response['pattern1'] = 'ログインしました';

	    }else{
	    	$this->response['pattern2'] = 'パスワードが一致しません';
	    } 
	}

	public function signup() { #クラス化に対応してなかったので、要修正！
		$this->signupData = $this->sql(
			'INSERT users(login_id,login_pw) VALUES(:inputId,:inputPw);',
			':inputId',$this->inputId,
			':inputPw',$this->inputPw
		); #このままだとIDが重複していた際に、どのようなエラー文が返ってくるのか不明なので怖い。
		
		$this->response['pattern3'] = '新規登録しました';

		login();
	}
}

$auth = new Authentication();

if( $signupFlag ) {
	$auth->signup();
} else {
	$auth->login();
}


echo json_encode( $response );
