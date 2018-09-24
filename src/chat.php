<?php
	require_once "include/models/session.php";
	
	if (!Session::isLoggedIn()){
		header("Location: login.php");
		exit();
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Home</title>
		
		<?php include "partials/_head.php"; ?>
	</head>
	<body>
		<?php include "partials/_header.php"; ?>
		<h1>Chat</h1>
		<form id="frmChat" method="POST">
			<div>
				<label>Message:</label>
				<textarea id="txtMessage" name="txtMessage"></textarea>
			</div>
			<button type="submit">Submit</button>
		</form>
	</body>
</html>