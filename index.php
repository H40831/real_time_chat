<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>リアルタイムチャット</title>
	<link rel="stylesheet" type="text/css" href="ress.css">
	<link rel="stylesheet" type="text/css" href="styles.css">

    <?php
    	require __DIR__.'/logic.php';
	?>

</head>
<body>

	<section id="loginWindow" class="center shadow">
		<form method="post">
			<div class="center">
				<p class="row">
					<span class="formName">ID</span>
					<input class="whiteBox shadow" type="text" name="id">
				</p>
				<p class="row">
					<span class="formName">PW</span>
					<input class="whiteBox shadow" type="password" name="pw">
				</p>
				<input id="loginButton" class="whiteBox shadow" type="submit" name="login" value="ログイン" onsubmit="alert('hoge')">
			</div>
		</form>
	</section>

	<section class="chatRoom">
		<form method="post" action="index.php">
		    <textarea name="message" class="whiteBox shadow"></textarea>
		    <!-- ↓buttonじゃなくてinputタグに直す！ -->
		    <button type="submit" name="sendMessage">発言</button>
		</form>

	    <?php
	    	$test = $db->prepare('SELECT user_name FROM users;');
	    	$test->execute();
	        $test_arr = $test->fetch();

	        echo '<p class="talker">'.$test_arr[0].': '.'</p>';
		    echo '<p class="bubble">'.$_POST['message'].'</p>';
		?>

	</section>

	<script type="text/javascript" src="index.js"></script>
</body>
</html>
