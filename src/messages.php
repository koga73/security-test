<?php
	require_once "include/models/session.php";
	require_once "include/db.php";
	
	if (!Session::isLoggedIn()){
		header("Location: login.php");
		exit();
	}

	function postMessage(){
		$txtMessage = (isset($_POST["txtMessage"])) ? $_POST["txtMessage"] : null;
		if (!$txtMessage){
			throw new Exception("txtMessage required");
		}

		$user = Session::getUser();
		(new DB())->insertMessage($user, $txtMessage);
	}

	$error = null;
	try {
		if (!empty($_POST)){
			postMessage();
		}
		$messages = ((new DB())->searchMessages());
	} catch (Exception $ex){
		$error = $ex->getMessage();
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Message board</title>
		
		<?php include "partials/_head.php"; ?>
	</head>
	<body>
	<?php include "partials/_header.php"; ?>
		<section>
			<form id="frmMessages" ref="form" method="POST" v-on:submit="handler_form_submit" v-cloak>
				<h1><span>Post a message</span></h1>
				<div class="input-wrap">
					<label for="txtMessage">Message:</label>
					<input type="text" id="txtMessage" name="txtMessage" ref="txtMessage" v-model="model.message" required minlength="1" maxlength="140" pattern="[\w`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}'|\\\s]+" v-on:invalid="handler_input_invalid" v-on:blur="handler_input_blur" v-bind:disabled="submitted"/>
					<span class="error">Must be from 1 to 140 characters</span>
				</div>
				<button type="submit" v-bind:disabled="submitted || incomplete">Post</button>
				<?php if ($error): ?>
					<span class="error server-error"><?php echo $error ?></span>
				<?php endif; ?>
			</form>
		</section>
		
		<canvas id="fusionCanvas"></canvas>
		<script src="js/_lib/GFXRenderer.min.js"></script>
		<script src="js/FusionRenderer.js"></script>
		
		<script src="js/_lib/vue.min.js"></script>
		<script src="js/messages.js"></script>
	</body>
</html>