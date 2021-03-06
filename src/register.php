<?php
	require_once "include/models/session.php";
	require_once "include/db.php";

	const POST_REDIRECT = "login.php";

/* @if USE_CAPTCHA */
	const USE_CAPTCHA = true;
/* @endif */
/* @if !USE_CAPTCHA */
	const USE_CAPTCHA = false;
/* @endif */
	const RECAPTCHA_SECRET = "6Lf6yW8UAAAAAD1EWi-l4utA6jyV7Rlr5Gc2WJ37";

	require_once "include/recaptcha/autoload.php";
	$recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_SECRET);

	function register($recaptcha){
		$nonce = (isset($_POST["nonce"])) ? $_POST["nonce"] : null;
		if (!Session::verifyNonce($nonce)){
			throw new Exception("nonce invalid");
		}

		$txtUser = (isset($_POST["txtUser"])) ? $_POST["txtUser"] : null;
		if (!$txtUser){
			throw new Exception("txtUser required");
		}
		$txtPass = (isset($_POST["txtPass"])) ? $_POST["txtPass"] : null;
		if (!$txtPass){
			throw new Exception("txtPass required");
		}

		$gRecaptchaResponse = (isset($_POST["g-recaptcha-response"])) ? $_POST["g-recaptcha-response"] : null;
		if (USE_CAPTCHA && !$gRecaptchaResponse){
			throw new Exception("reCAPTCHA required");
		}
		if (USE_CAPTCHA){
			$recaptchaResp = $recaptcha->setExpectedHostname("localhost")->verify($gRecaptchaResponse, getUserIP());
			if (!$recaptchaResp->isSuccess()){
				throw new Exception("reCAPTCHA invalid"); // . " " . $recaptchaResp->getErrorCodes();
			}
		}

		DB::insertUser($txtUser, $txtPass);
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
		try {
			register($recaptcha);
			header("Location: " . Session::appendToUrl(POST_REDIRECT));
			exit();
		} catch (Exception $ex){
			$error = $ex->getMessage();
		}
	}
	$nonce = Session::generateNonce();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Register</title>

		<?php include "partials/_head.php"; ?>

		<?php if (USE_CAPTCHA) echo '<script src="https://www.google.com/recaptcha/api.js"></script>' ?>
	</head>
	<body>
		<?php include "partials/_header.php"; ?>
		<section class="center">
			<h1>Register</h1>
			<form id="frmRegister" ref="form" method="POST" action="<?php echo Session::appendToUrl('') ?>" v-on:submit="handler_form_submit" v-cloak>
				<div class="input-wrap">
					<label for="txtUser">Username:</label>
					<input type="text" id="txtUser" name="txtUser" autocomplete="off" ref="txtUser" v-model="model.user" required minlength="6" maxlength="16" pattern="[\w]+" v-on:invalid="handler_input_invalid" v-on:blur="handler_input_blur" v-bind:disabled="submitted"/>
					<span class="error">Must be from 6 to 16 characters</span>
				</div>
				<div class="input-wrap">
					<label for="txtPass">Password:</label>
					<input type="password" id="txtPass" name="txtPass" v-model="model.pass" required minlength="7" maxlength="32" pattern="[\w\s`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}|\\']+" v-on:invalid="handler_input_invalid" v-on:blur="handler_input_blur" v-on:input="handler_pass_input" v-bind:disabled="submitted"/>
					<span class="error">Must be from 7 to 32 characters</span>
				</div>
				<div class="input-wrap">
					<label for="txtPassConfirm">Confirm Password:</label>
					<input type="password" id="txtPassConfirm" name="txtPassConfirm" ref="txtPassConfirm" v-model="model.passConfirm" required minlength="7" maxlength="32" pattern="[\w\s`~!@#$%^&*()-=+,<\.>\/?;:\[{\]}|\\']+" v-on:invalid="handler_input_invalid" v-on:blur="handler_input_blur" v-on:input="handler_pass_input" v-bind:disabled="submitted"/>
					<span class="error">Must be from 7 to 32 characters</span>
				</div>
				<?php if (USE_CAPTCHA): ?>
					<div class="input-wrap">
						<div class="g-recaptcha" data-sitekey="6Lf6yW8UAAAAAGWoH6M3wxyiPCuBMZXnLNbCcBGj" data-theme="dark"></div>
					</div>
				<?php endif; ?>
				<button type="submit" v-bind:disabled="submitted || incomplete">Register</button>
				<?php if ($error): ?>
					<span class="error server-error"><?php echo htmlspecialchars($error) ?></span>
				<?php endif; ?>
				<input type="hidden" name="nonce" value="<?php echo htmlspecialchars($nonce) ?>"/>
			</form>
		</section>

		<canvas id="fusionCanvas"></canvas>
		<script src="js/_lib/GFXRenderer.min.js"></script>
		<script src="js/FusionRenderer.js"></script>

		<script src="js/_lib/vue.min.js"></script>
		<script src="js/register.js"></script>
	</body>
</html>