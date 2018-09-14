<?php
	define("USE_CAPTCHA", true);
	define("DB_ADDRESS", "127.0.0.1");
	define("DB_USER", "root");
	define("DB_PASS", "");
	define("DB_NAME", "security_test");
	define("RECAPTCHA_SECRET", "6Lf6yW8UAAAAAD1EWi-l4utA6jyV7Rlr5Gc2WJ37");

	require_once "../recaptcha-master/src/autoload.php";
	$recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_SECRET);

	if (!empty($_POST)){
		$txtUser = (isset($_POST["txtUser"])) ? $_POST["txtUser"] : null;
		$txtPass = (isset($_POST["txtPass"])) ? $_POST["txtPass"] : null;
		$gRecaptchaResponse = (isset($_POST["g-recaptcha-response"])) ? $_POST["g-recaptcha-response"] : null;
		if ($txtUser && $txtPass && ($gRecaptchaResponse || !USE_CAPTCHA)){
			//Validate txtUser
			if (!preg_match("/^\\w{6,16}$/", $txtUser)){
				die("Invalid username");
			}
			//Validate txtPass
			if (!preg_match("/^[\\w`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}|\\\s]{7,32}$/", $txtPass)){
				die("Invalid password");
			}
			//Validate captcha
			if (USE_CAPTCHA){
				$recaptchaResp = $recaptcha->setExpectedHostname("localhost")->verify($gRecaptchaResponse, getUserIP());
				if (!$recaptchaResp->isSuccess()){
					die("reCAPTCHA failed " . $recaptchaResp->getErrorCodes());
				}
			}

			//DB Connection
			$conn = new mysqli(DB_ADDRESS, DB_USER, DB_PASS, DB_NAME);
			$connError = mysqli_connect_error();
			if ($connError){
				die("Database connection failed: " . $connError);
			}
			
			//Parameterized SQL to secure against SQLi
			$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
			if (!$stmt){
				die("Invalid statement");
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

			die("Success!");
		} else {
			die("Incomplete form data");
		}
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
		<section class="wrap">
			<h1>Register</h1>
			<form id="frmRegister" ref="form" method="POST" v-on:submit="handler_frmRegister_submit">
				<div class="input-wrap">
					<label for="txtUser">Username:</label>
					<input type="text" id="txtUser" name="txtUser" v-model="model.user" required minlength="6" maxlength="16" pattern="[\w]+" v-on:invalid="handler_input_invalid" v-on:blur="handler_input_blur" v-bind:disabled="submitted"/>
					<span class="error">Please enter a valid username between 6 and 16 characters</span>
				</div>
				<div class="input-wrap">
					<label for="txtPass">Password:</label>
					<input type="password" id="txtPass" name="txtPass" v-model="model.pass" required minlength="7" maxlength="32" pattern="[\w`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}|\\\s]+" v-on:invalid="handler_input_invalid" v-on:blur="handler_input_blur" v-on:input="handler_pass_input" v-bind:disabled="submitted"/>
					<span class="error">Please enter a valid password between 7 and 32 characters</span>
				</div>
				<div class="input-wrap">
					<label for="txtPassConfirm">Confirm Password:</label>
					<input type="password" id="txtPassConfirm" name="txtPassConfirm" ref="txtPassConfirm" v-model="model.passConfirm" required minlength="7" maxlength="32" pattern="[\w`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}|\\\s]+" v-on:invalid="handler_input_invalid" v-on:blur="handler_input_blur" v-on:input="handler_pass_input" v-bind:disabled="submitted"/>
					<span class="error">Please enter a valid password between 7 and 32 characters</span>
				</div>
				<?php if (USE_CAPTCHA) echo '<div class="g-recaptcha" data-sitekey="6Lf6yW8UAAAAAGWoH6M3wxyiPCuBMZXnLNbCcBGj" data-theme="dark"></div>' ?>
				<button type="submit" v-bind:disabled="submitted">Register</button>
			</form>
		</section>
		
		<script src="js/_lib/vue.min.js"></script>
		<script src="js/register.js"></script>
	</body>
</html>