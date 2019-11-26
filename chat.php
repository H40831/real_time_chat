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
	<header class="shadow nonActive">
		<button id="roomMenuButton"></button><!-- class属性の値はJavaScriptのroomMenuToggle()で管理 -->
		<p id="roomId">たのしいチャットルーム</p>
		<button id="logoutButton" class="whiteBox shadow button" onClick="location.href='logout.php'">ログアウト</button>
	</header>

	<section id="roomMenu" class="<?= $roomMenuFlag ?>"><!-- class属性の値はPHPの$roomMenuFlagと、JavaScriptのswitchRoomMenu()で管理 -->
		<table id="roomList">
			<tr>
				<td>部屋名部屋名部屋名部屋名部屋名部屋名部屋名部屋名部屋名部屋名</td>
				<td>人数</td>
				<td>状態</td>
			</tr>
			<tr>
				<td>部屋名部屋名部屋名部屋名部屋名部屋名部屋名部屋名部屋名部屋名</td>
				<td>人数</td>
				<td>状態</td>
			</tr>
			<tr>
				<td>部屋名部屋名部屋名部屋名部屋名部屋名部屋名部屋名部屋名部屋名</td>
				<td>人数</td>
				<td>状態</td>
			</tr>
			<tr>
				<td>部屋名部屋名部屋名部屋名部屋名部屋名部屋名部屋名部屋名部屋名</td>
				<td>人数</td>
				<td>状態</td>
			</tr>
		</table>
	</section>

	<section id="chatWindow">
		<div id="chatLog">
			

		</div>

		<form id="messageForm" class="shadow nonActive">
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
