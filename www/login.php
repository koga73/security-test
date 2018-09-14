<?php
	define("DB_ADDRESS", "127.0.0.1");
	define("DB_USER", "root");
	define("DB_PASS", "");
	define("DB_NAME", "security_test");
	
	function process($recaptcha){
		$txtUser = (isset($_POST["txtUser"])) ? $_POST["txtUser"] : null;
		if (!$txtUser){
			return "txtUser required";
		}
		if (!preg_match("/^\\w{6,16}$/", $txtUser)){
			return "txtUser invalid";
		}

		$txtPass = (isset($_POST["txtPass"])) ? $_POST["txtPass"] : null;
		if (!$txtPass){
			return "txtPass required";
		}
		if (!preg_match("/^[\\w`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}|\\\s]{7,32}$/", $txtPass)){
			return "txtPass invalid";
		}

		//Success
		return 0;
	}

	$error = null;
	if (!empty($_POST)){
		$error = process();
		if (!$error){
			//Redirect
			header("Location: chat.php");
			exit();
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
	<body>
		<section>
			<form id="frmLogin" ref="form" method="POST" v-on:submit="handler_frmLogin_submit" v-cloak>
				<h1><span>Login</span></h1>
				<div class="input-wrap">
					<label for="txtUser">Username:</label>
					<input type="text" id="txtUser" name="txtUser" v-model="model.user" required minlength="6" maxlength="16" pattern="[\w]+" v-on:invalid="handler_input_invalid" v-on:blur="handler_input_blur" v-bind:disabled="submitted"/>
					<span class="error">Must be from 6 to 16 characters</span>
				</div>
				<div class="input-wrap">
					<label for="txtPass">Password:</label>
					<input type="password" id="txtPass" name="txtPass" v-model="model.pass" required minlength="7" maxlength="32" pattern="[\w`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}|\\\s]+" v-on:invalid="handler_input_invalid" v-on:blur="handler_input_blur" v-on:input="handler_pass_input" v-bind:disabled="submitted"/>
					<span class="error">Must be from 7 to 32 characters</span>
				</div>
				<button type="submit" v-bind:disabled="submitted || incomplete">Login</button>
				<?php if ($error): ?>
					<span class="error server-error"><?php echo $error ?></span>
				<?php endif; ?>
			</form>
		</section>
		
		<script src="js/_lib/vue.min.js"></script>
		<script src="js/login.js"></script>
	</body>
</html>