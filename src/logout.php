<?php
	require_once "include/models/session.php";

	Session::logout();

	//No need to append session since it is being cleared
	header("Location: login.php");
	exit();
?>