<?php
	session_start();
	$_SESSION = [];
	session_destroy();
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/login');
	exit;