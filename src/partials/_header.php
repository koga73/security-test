<?php
	require_once "include/models/session.php";
	
	$isLoggedIn = Session::isLoggedIn();
?>
<header>
	<nav>
		<ul>
			<li>
				<a href="/">Home</a>
			</li>
			<?php if ($isLoggedIn): ?>
				<li>
					<a href="logout.php">Logout</a>
				</li>
			<?php else: ?>
				<li>
					<a href="login.php">Login</a>
				</li>
				<li>
					<a href="register.php">Register</a>
				</li>
			<?php endif; ?>
			<li>
				<a href="search.php">Search</a>
			</li>
		</ul>
	</nav>
	<?php ?>
</header>