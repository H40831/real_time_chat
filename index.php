<?php
	require_once __DIR__.'/main.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>リアルタイムチャット</title>
	<link rel="stylesheet" type="text/css" href="ress.css">
	<link rel="stylesheet" type="text/css" href="styles.css">
	<!-- Google Material Icons --> <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>

	<section id="loginWindow" class="center shadow nonActive">
		<div class="center">
			<form method="post" id="loginForm">
				<p class="row">
					<span class="formName">ID</span>
					<input id="id" name="id" class="whiteBox shadow" type="text">
				</p>
				<p class="row">
					<span class="formName">PW</span>
					<input id="pw" name="pw" class="whiteBox shadow" type="password">
				</p>

			</form>
			<p class="row">
				<button id="loginButton" class="whiteBox shadow button">ログイン</button>
				<button id="signupButton" class="whiteBox shadow button">新規登録</button>
			</p>
		</div>	
	</section>

	<section class="chatRoom">
		<form method="post" action="index.php">
		    <textarea name="message" class="whiteBox shadow"></textarea>
		    <!-- ↓buttonじゃなくてinputタグに直す！ -->
		    <button type="submit" name="sendMessage" class="whiteBox button">発言</button>
		</form>

	</section>

	<script type="text/javascript" src="main.js"></script>
</body>
</html>
