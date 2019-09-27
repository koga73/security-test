<?php
	require_once "include/models/session.php";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Page Not Found</title>
		
		<?php include "partials/_head.php"; ?>
	</head>
	<body>
		<?php include "partials/_header.php"; ?>
		<section class="center">
			<h1>Page not found!</h1>
/* @if !SECURE */
	/* @if !HIDDEN_COMMENTS */
			//Vulnerability: Reflected XSS
			//Fix: Don't do this, just omit showing the invalid path!
	/* @endif */
			<strong><?php echo urldecode($_SERVER['REQUEST_URI']) ?></strong>
/* @endif */
		</section>
		
		<canvas id="fusionCanvas"></canvas>
		<script src="js/_lib/GFXRenderer.min.js"></script>
		<script src="js/FusionRenderer.js"></script>
	</body>
</html>