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
				<li><strong>Don't destroy anything</strong> (drop tables / delete files)</li>
				<li><strong>Don't mess with others</strong> (annoying alerts, DoS)</li>
				<li><strong>Don't fix vulnerabilities</strong></li>
			</ol>
		</section>
		<section>
			<h2>Vulnerabilities &amp; Scoring</h2>
			<p>
				Document where the vulnerability exists and how to exploit it
				<br/>
				<strong>+2 points</strong> per vulnerability for accurate how-to-fix recommendation
			</p>
			<ul>
				<li class="easy">XSS - console.log your name <strong>1 Point</strong></li>
				<li class="easy">CSRF <strong>2 Points</strong></li>
				<li class="easy">Session fixation <strong>2 Points</strong></li>
				<li class="medium">SQLi - obtain password hashes <strong>3 Points</strong></li>
				<li class="medium">Password cracking <strong>4 Points</strong></li>
				<li class="hard">Homepage defacement (Add your name below) <strong>5 Points</strong></li>
				<li class="hard">TLS Private Key obtained <strong>6 Points</strong></li>
				<li class="hard">Root access <strong>7 Points</strong></li>
			</ul>
			<p>Find anything else?</p>
		</section>
		<section>
			<h3>Add your name here</h3>
			<ul>
				<li>No names yet</li>
			</li>
		</section>
	</body>
</html>