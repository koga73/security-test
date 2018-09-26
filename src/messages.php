<?php
	require_once "include/models/session.php";
	require_once "include/db.php";
	
	if (!Session::isLoggedIn()){
		header("Location: login.php");
		exit();
	}
	$user = Session::getUser();

	function postMessage(){
		$nonce = (isset($_POST["nonce"])) ? $_POST["nonce"] : null;
		if (!Session::verifyNonce($nonce)){
			//throw new Exception("nonce invalid");
		}
		
		$txtMessage = (isset($_POST["txtMessage"])) ? $_POST["txtMessage"] : null;
		if (!$txtMessage){
			throw new Exception("txtMessage required");
		}

		$user = Session::getUser();
		(new DB())->insertMessage($user, $txtMessage);
	}

	$error = null;
	$messages = [];
	try {
		if (!empty($_POST)){
			postMessage();
		}
		$messages = ((new DB())->searchMessages());
	} catch (Exception $ex){
		$error = $ex->getMessage();
	}
	$nonce = Session::generateNonce();
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
			<h1>Post a message</h1>
			<form id="frmMessages" ref="form" method="POST" v-on:submit="handler_form_submit" v-cloak>
				<div class="input-wrap">
					<label for="txtMessage">Message:</label>
					<input type="text" id="txtMessage" name="txtMessage" autocomplete="off" ref="txtMessage" v-model="model.message" required minlength="1" maxlength="140" pattern="[\w`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}'|\\\s]+" v-on:invalid="handler_input_invalid" v-on:blur="handler_input_blur" v-bind:disabled="submitted"/>
					<span class="error">Must be from 1 to 140 characters</span>
				</div>
				<button type="submit" v-bind:disabled="submitted || incomplete">Post</button>
				<?php if ($error): ?>
					<span class="error server-error"><?php echo $error ?></span>
				<?php endif; ?>
				<input type="hidden" name="nonce" value="<?php echo $nonce; ?>"/>
			</form>
		</section>
		<section>
			<h2>Messages</h2>
			<ol class="messages">
				<?php foreach($messages as &$message): ?>
					<li class="<?php echo ($message->username == $user->username) ? 'self' : ''; ?>">
						<span class="user"><?php echo $message->username ?></span>
						<span class="content"><?php echo $message->content ?></span>
						<span class="created"><?php echo $message->created ?></span>
					</li>
				<?php endforeach; ?>
			</ol>
		</section>
		
		<canvas id="fusionCanvas"></canvas>
		<script src="js/_lib/GFXRenderer.min.js"></script>
		<script src="js/FusionRenderer.js"></script>
		
		<script src="js/_lib/vue.min.js"></script>
		<script src="js/messages.js"></script>
	</body>
</html>