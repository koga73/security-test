<?php
	require_once "include/models/session.php";

	Session::logout();

	header('Location: login.php');
	exit();
?>