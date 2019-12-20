<?php
	session_start();
	if (isset($_SESSION['user_id'])) {
		header('Location: http://'.$_SERVER['HTTP_HOST'].'/chat');
        exit;
    }
    function css($paths){
    	forEach($paths as $path){
    		echo "<link rel='stylesheet' type='text/css' href='styles/{$path}'>";
    	}
    }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>リアルタイムチャット</title>
	<link rel="shortcut icon" href="favicon.ico">
	<?php css(["ress.css","common.css","login.css"]); ?>
	<!-- Font Awesome --> <script src="https://kit.fontawesome.com/2d2bcba3f8.js" crossorigin="anonymous"></script>
</head>
<body>

	<section id="loginWindow" class="center shadow nonActive">
		<div class="center">
			<form id="loginForm">
				<p class="row">
					<span class="formName">ID</span>
					<input id="id" name="id" class="whiteBox shadow" type="text">
				</p>
				<p class="row">
					<span class="formName">PW</span>
					<input id="pw" name="pw" class="whiteBox shadow" type="password">
					<button id="showPwButton" class="far fa-eye-slash" type="button" tabindex="-1"></button>
				</p>

			</form>
			<small  id="loginFormMessage"></small><br>
			<p class="row">
				<button id="loginButton" class="whiteBox shadow button">ログイン</button>
				<button id="signupButton" class="whiteBox shadow button">新規登録</button>
			</p>
		</div>	
	</section>

	<script type="text/javascript" src="logics/js/login_logic.js"></script>
</body>
</html>
