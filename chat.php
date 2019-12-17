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

	$roomMenuFlag = empty($_SESSION['current_room']) ? "hide" : "";//JavaScriptのswitchRoomMenu()が発動するため、この時点ではフラグが逆になる。

	function css($paths){
    	forEach($paths as $path){
    		echo "<link rel='stylesheet' type='text/css' href='$path'>";
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
	<?php css(["ress.css","common.css","chat_header.css","chat_body.css"]); ?>
	<!-- Font Awesome --> <script src="https://kit.fontawesome.com/2d2bcba3f8.js" crossorigin="anonymous"></script>
    <script src="http://ec2-52-195-2-97.ap-northeast-1.compute.amazonaws.com:8080/socket.io/socket.io.js"></script>
    <script src="moment.js"></script>
</head>
<body>
	<header id="headMenu" class="chatMenu shadow nonActive">
		<p id="roomName"></p>
		<button id="logoutButton" class="whiteBox shadow button" onClick="location.href='logout.php'">ログアウト</button>
	</header>
	<button id="roomMenuButton"></button><!-- class属性の値はJavaScriptのroomMenuToggle()で管理 -->

	<section id="roomMenu" class="<?= $roomMenuFlag ?>"><!-- class属性の値はPHPの$roomMenuFlagと、JavaScriptのswitchRoomMenu()で管理 -->
		<ul id="roomList">
		</ul>
		<div id="addRoomForm">
			<div class="rows">
				<p class="row">
					<span class="top formName">RoomName</span>
					<input id="" type="text" class="whiteBox shadow">
				</p>
				<p class="row">
					<span class="top formName">Member</span>
					<input id="" type="text" class="whiteBox shadow">
				</p>
			</div>
			<div id="addRoomButtons">
				<button id="addRoom" class="fas fa-plus floatButton shadow center" data-label="Room"></button>
			</div>	
		</div>
	</section>

	<section id="chatWindow">
		<div id="chatLog"></div>

		<form id="messageForm" class="chatMenu shadow nonActive">
			<p class="row">
				<span class="formName">Name</span>
				<input id="nameArea" type="text" name="name" class="whiteBox shadow">
			</p>
			<p id="messageAreaRow" class="row">
			    <textarea id="messageArea" name="message" class="whiteBox shadow"></textarea>
			    <button id="sendMessage" class="whiteBox shadow button far fa-comment"></button>
		    	
		    </p>
		</form>

	</section>
	<script type="text/javascript" src="chat_logic.js"></script>
</body>
</html>
