<?php
	require_once "include/models/session.php";
	require_once "include/db.php";
	
	$error = null;
	$messages = [];
	try {
		$q = (isset($_GET["q"])) ? $_GET["q"] : "";
		$messages = DB::searchMessages($q);
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
			<form method="GET">
				<h1>Search</h1>
				<div class="input-wrap">
					<label for="txtSearch">Search:</label>
					<input type="text" id="txtSearch" name="q" autocomplete="off" value="<?php echo htmlspecialchars($q) ?>"/>
				</div>
				<button type="submit">Search</button>
			</form>
		</section>
		<section id="results">
			<h1>Results</h1>
			<?php if ($error): ?>
				<span class="error server-error"><?php echo htmlspecialchars($error) ?></span>
			<?php endif; ?>
			<?php if (count($messages)): ?>
				<table>
					<tr>
						<?php foreach($messages[0] as $key => $value): ?>
							<th><?php echo htmlspecialchars($key) ?></th>
						<?php endforeach; ?>
					</tr>
					<?php foreach($messages as &$message): ?>
						<tr>
							<?php foreach($message as $key => $value): ?>
								<td><?php echo htmlspecialchars($value) ?></td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endif; ?>
		</section>
		
		<canvas id="fusionCanvas"></canvas>
		<script src="js/_lib/GFXRenderer.min.js"></script>
		<script src="js/FusionRenderer.js"></script>
	</body>
</html>