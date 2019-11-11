<?php

ini_set('display_errors', 1);
#error_reporting(-1);

require_once __DIR__."/db_access.php";

header('Content-Type: application/json');
$inputId = $_POST['id'];
$inputPw = $_POST['pw'];

if($_POST['loginOrSignup']==="signup"){
	$signupFlag = true;
}else{
	$signupFlag = false;
}

$response = [];

class Authentication extends MySql {
	public function login() {
		$userInfo = $this->sql(
			'SELECT * FROM users WHERE login_id = :inputId;',
			':inputId',$inputId
		);


		if( password_verify($inputPw, $userInfo['login_pw']) ){
	        #ログイン成功 #成功時の処理書く
	        $response['pattern1'] = 'ログインしました';

	    }else{
	    	$response['pattern2'] = 'パスワードが一致しません';
	    } 
	}

	public function signup() { #クラス化に対応してなかったので、要修正！
		$signupData = $this->sql(
			'INSERT users(login_id,login_pw) VALUES(:inputId,:inputPw);',
			':inputId',$inputId,
			':inputPw',$inputPw
		); #このままだとIDが重複していた際に、どのようなエラー文が返ってくるのか不明なので怖い。
		$response['pattern3'] = '新規登録しました';

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
