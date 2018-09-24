<?php
	require_once "include/models/session.php";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Rules</title>
		
		<?php include "partials/_head.php"; ?>
	</head>
	<body>
		<?php include "partials/_header.php"; ?>
		<section>
			<h1>Rules</h1>
			<ol>
				<li>Don't destroy anything</li>
				<li>Don't mess with others</li>
				<li>Don't fix vulnerabilities</li>
			</ol>
		</section>
		<section>
			<h2>Vulnerabilities &amp; Scoring</h2>
			<p>Document where the vulnerability exists and how to exploit it</p>
			<p>+2 points per vulnerability for how-to-fix recommendation</p>
			<ul>
				<li>SQLi <strong>1 Point</strong></li>
				<li>XSS <strong>1 Point</strong></li>
				<li>CSRF <strong>2 Points</strong></li>
				<li>Response splitting <strong>3 Points</strong></li>
				<li>Password cracking <strong>4 Points</strong></li>
				<li>Session Hijacking <strong>4 Points</strong></li>
				<li>Homepage defacement (Add your name below) <strong>5 Points</strong></li>
				<li>TLS Private Key obtained <strong>10 Points</strong></li>
			</ul>
		</section>
		<section>
			<h3>31337 HAck3rs</h3>
			<p>Add your name here</p>
		</section>
	</body>
</html>