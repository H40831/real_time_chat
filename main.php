<?php
ini_set('display_errors', 1);
error_reporting(-1);

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
	<section class="chatRoom">
		<form method="post" action="index.php">
		    <textarea name="message" class="whiteBox shadow"></textarea>
		    <!-- ↓buttonじゃなくてinputタグに直す？ -->
		    <button type="submit" name="sendMessage" class="whiteBox button">発言</button>
		</form>

	</section>

	<script type="text/javascript" src="main.js"></script>
</body>
</html>
