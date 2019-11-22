<?php
session_start();
ini_set('display_errors', 1);
error_reporting(-1);

if (!isset($_SESSION['user_id'])) {
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/login.php');
    exit;
}

require_once __DIR__."/vendor/autoload.php";
require_once __DIR__."/db_access.php";

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>リアルタイムチャット</title>
	<link rel="stylesheet" type="text/css" href="ress.css">
	<link rel="stylesheet" type="text/css" href="styles.css">
	<!-- Font Awesome --> <script src="https://kit.fontawesome.com/2d2bcba3f8.js" crossorigin="anonymous"></script>
</head>
<body>
	<section id="chatWindow">
		<div id="chatLog">
			

		</div>

		<form id="messageForm" class="shadow nonActive">
			<p class="row">
				<span class="formName">なまえ</span>
				<input id="nameArea" type="text" name="name" class="whiteBox shadow">
			</p>
		    <textarea id="messageArea" name="message" class="whiteBox shadow"></textarea>
		    <!-- ↓buttonじゃなくてinputタグに直す？ -->
		    <button id="sendMessage" class="whiteBox shadow button">発言</button>
		</form>

	</section>
	<button id="logoutButton" class="whiteBox shadow button" onClick="location.href='logout.php'">ログアウト</button>
	<script type="text/javascript" src="chat_logic.js"></script>
</body>
</html>
