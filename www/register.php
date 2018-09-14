<?php
	define("USE_CAPTCHA", true);
	define("DB_ADDRESS", "127.0.0.1");
	define("DB_USER", "root");
	define("DB_PASS", "");
	define("DB_NAME", "security_test");
	define("RECAPTCHA_SECRET", "6Lf6yW8UAAAAAD1EWi-l4utA6jyV7Rlr5Gc2WJ37");
	
	require_once "../recaptcha-master/src/autoload.php";
	$recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_SECRET);

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

		$gRecaptchaResponse = (isset($_POST["g-recaptcha-response"])) ? $_POST["g-recaptcha-response"] : null;
		if (USE_CAPTCHA && !$gRecaptchaResponse){
			return "reCAPTCHA required";
		}
		if (USE_CAPTCHA){
			$recaptchaResp = $recaptcha->setExpectedHostname("localhost")->verify($gRecaptchaResponse, getUserIP());
			if (!$recaptchaResp->isSuccess()){
				return "reCAPTCHA invalid"; // . " " . $recaptchaResp->getErrorCodes();
			}
		}

		//DB Connection
		$conn = new mysqli(DB_ADDRESS, DB_USER, DB_PASS, DB_NAME);
		$connError = mysqli_connect_error();
		if ($connError){
			return "Database connection failed"; // . " " . $connError);
		}
		
		//Parameterized SQL to secure against SQLi
		$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
		if (!$stmt){
			return "Statement invalid";
		}
		$stmt->bind_param("ss", $username, $password);

		//Insert user
		$username = $txtUser;
		//Vulnerability: Password hashed using outdated SHA1 and not salted!
		//Fix: Use bcrypt hashing and salt with username + constant
		$password = sha1($txtPass);
		$stmt->execute();
		$stmt->close();

		$conn->close();

		//Success
		return 0;
	}

	function getUserIP() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}

	$error = null;
	if (!empty($_POST)){
		$error = process($recaptcha);
		if (!$error){
			//Redirect
			header("Location: login.php");
			exit();
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Register</title>
		
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width,initial-scale=1"/>
		
		<link rel="stylesheet" href="css/styles.css" type="text/css"/>

		<?php if (USE_CAPTCHA) echo '<script src="https://www.google.com/recaptcha/api.js"></script>' ?>
	</head>
	<body>
		<section>
			<form id="frmRegister" ref="form" method="POST" v-on:submit="handler_frmRegister_submit" v-cloak>
				<h1><span>Register</span></h1>
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
				<div class="input-wrap">
					<label for="txtPassConfirm">Confirm Password:</label>
					<input type="password" id="txtPassConfirm" name="txtPassConfirm" ref="txtPassConfirm" v-model="model.passConfirm" required minlength="7" maxlength="32" pattern="[\w`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}|\\\s]+" v-on:invalid="handler_input_invalid" v-on:blur="handler_input_blur" v-on:input="handler_pass_input" v-bind:disabled="submitted"/>
					<span class="error">Must be from 7 to 32 characters</span>
				</div>
				<?php if (USE_CAPTCHA): ?>
					<div class="input-wrap">
						<div class="g-recaptcha" data-sitekey="6Lf6yW8UAAAAAGWoH6M3wxyiPCuBMZXnLNbCcBGj" data-theme="dark"></div>
					</div>
				<?php endif; ?>
				<button type="submit" v-bind:disabled="submitted || incomplete">Register</button>
				<?php if ($error): ?>
					<span class="error server-error"><?php echo $error ?></span>
				<?php endif; ?>
			</form>
		</section>
		
		<script src="js/_lib/vue.min.js"></script>
		<script src="js/register.js"></script>
	</body>
</html>