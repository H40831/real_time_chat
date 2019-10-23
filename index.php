<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>リアルタイムチャット</title>
	<link rel="stylesheet" type="text/css" href="ress.css">
	<link rel="stylesheet" type="text/css" href="styles.css">

    <?php
	    ini_set('display_errors', 1);
	    require_once __DIR__."/vendor/autoload.php";
        $dotenv = Dotenv\Dotenv::create(__DIR__);
	    $dotenv->load();
        try {
	        $db = new PDO(
	            'mysql:host=my-rds.c7rekwisb6xy.ap-northeast-1.rds.amazonaws.com;dbname=real_time_chat;charset=utf8mb4',
	            $_ENV['DB_ID'],
	            $_ENV['DB_PW'],
	            array(
	                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	                PDO::ATTR_EMULATE_PREPARES => false,
	            )
                // 実行する処理
            );
		} catch(PDOException $e) {
	        // エラー時の処理
	        $error = $e->getMessage();
	    }
	?>

</head>
<body>

	<section class="loginWindow center shadow">
		<form method="post" action="index.php">
			<div class="center">
				<p class="row">
					<span class="formName">ID</span>
					<input type="text" name="id" class="whiteBox shadow">
				</p>
				<p class="row">
					<span class="formName">PW</span>
					<input type="password" name="pw" class="whiteBox shadow">
				</p>
				<input class="whiteBox" type="submit" name="login" value="ログイン">
			</div>
		</form>
	</section>

	<section class="chatRoom">
		<form method="post" action="index.php">
		    <textarea name="message" class="whiteBox"></textarea>
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
