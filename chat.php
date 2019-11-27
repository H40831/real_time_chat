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

$roomMenuFlag = empty($_SESSION['currentRoom']) ? "hide" : "";//JavaScriptのswitchRoomMenu()が発動するため、この時点ではフラグが逆になる。

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>リアルタイムチャット</title>
	<link rel="shortcut icon" href="favicon.ico">
	<link rel="stylesheet" type="text/css" href="ress.css">
	<link rel="stylesheet" type="text/css" href="styles.css">
	<!-- Font Awesome --> <script src="https://kit.fontawesome.com/2d2bcba3f8.js" crossorigin="anonymous"></script>
</head>
<body>
	<header id="headMenu" class="chatMenu shadow nonActive">
		<p id="roomName">たのしいチャットルーム</p>
		<button id="logoutButton" class="whiteBox shadow button" onClick="location.href='logout.php'">ログアウト</button>
	</header>
	<button id="roomMenuButton"></button><!-- class属性の値はJavaScriptのroomMenuToggle()で管理 -->

	<section id="roomMenu" class="<?= $roomMenuFlag ?>"><!-- class属性の値はPHPの$roomMenuFlagと、JavaScriptのswitchRoomMenu()で管理 -->
		<ul id="roomList">
			<li>サンプルのチャットルーム</li>
			<li>この部屋はダミーですこの部屋はダミーですこの部屋はダミーですこの部屋はダミーですこの部屋はダミーですこの部屋はダミーですこの部屋はダミーですこの部屋はダミーですこの部屋はダミーですこの部屋はダミーですこの部屋はダミーですこの部屋はダミーですこの部屋はダミーですこの部屋はダミーです</li>
			<li>たのしいチャットルーム</li>
			<li>改行テスト<br>改行テスト<br>改行テスト<br>改行テスト<br>改行テスト<br>改行テスト<br>改行テスト<br>改行テスト<br>改行テスト<br>改行テスト<br>改行テスト</li>
		</table>
	</section>

	<section id="chatWindow">
		<div id="chatLog">
			

		</div>

		<form id="messageForm" class="chatMenu shadow nonActive">
			<p class="row">
				<span class="formName">なまえ</span>
				<input id="nameArea" type="text" name="name" class="whiteBox shadow">
			</p>
			<p id="messageAreaRow" class="row">
			    <textarea id="messageArea" name="message" class="whiteBox shadow"></textarea>
			    <button id="sendMessage" class="whiteBox shadow button">発言</button>
		    </p>
		</form>

	</section>
	<script type="text/javascript" src="chat_logic.js"></script>
</body>
</html>
