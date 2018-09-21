<?php
	require_once "../src/db.php";

	function process(){
		$txtUser = (isset($_POST["txtUser"])) ? $_POST["txtUser"] : null;
		if (!$txtUser){
			throw new Exception("txtUser required");
		}
		$txtPass = (isset($_POST["txtPass"])) ? $_POST["txtPass"] : null;
		if (!$txtPass){
			throw new Exception("txtPass required");
		}

		$user = (new DB())->login($txtUser, $txtPass);

		//TODO: Session!
	}

	$error = null;
	if (!empty($_POST)){
		try {
			process();
			header("Location: chat.php");
			exit();
		} catch (Exception $ex){
			$error = $ex->getMessage();
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Login</title>
		
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width,initial-scale=1"/>
		
		<link rel="stylesheet" href="css/styles.css" type="text/css"/>
	</head>
	<body>
		<section>
			<form id="frmLogin" ref="form" method="POST" v-on:submit="handler_form_submit" v-cloak>
				<h1><span>Login</span></h1>
				<div class="input-wrap">
					<label for="txtUser">Username:</label>
					<input type="text" id="txtUser" name="txtUser" v-model="model.user" required minlength="6" maxlength="16" pattern="[\w]+" v-on:invalid="handler_input_invalid" v-on:blur="handler_input_blur" v-bind:disabled="submitted"/>
					<span class="error">Must be from 6 to 16 characters</span>
				</div>
				<div class="input-wrap">
					<label for="txtPass">Password:</label>
					<input type="password" id="txtPass" name="txtPass" v-model="model.pass" required minlength="7" maxlength="32" pattern="[\w`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}|\\\s]+" v-on:invalid="handler_input_invalid" v-on:blur="handler_input_blur" v-bind:disabled="submitted"/>
					<span class="error">Must be from 7 to 32 characters</span>
				</div>
				<button type="submit" v-bind:disabled="submitted || incomplete">Login</button>
				<?php if ($error): ?>
					<span class="error server-error"><?php echo $error ?></span>
				<?php endif; ?>
			</form>
			<a href="Register.php">Don't have a login? Register here</a>
		</section>
		<canvas id="fusionCanvas"></canvas>
		
		<script src="js/_lib/vue.min.js"></script>
		<script src="js/_lib/GFXRenderer.min.js"></script>
		<script src="js/FusionRenderer.js"></script>
		<script src="js/login.js"></script>
	</body>
</html>