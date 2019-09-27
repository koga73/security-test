<?php
	require_once "include/models/session.php";

	$isLoggedIn = Session::isLoggedIn();
?>
<header class="clr">
	<div id="logo">
		<a href="/">
			<span class="seo">Home</span>
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 996.57 875.61"><defs><style>.a{fill:none;stroke:#FFF;stroke-miterlimit:10;stroke-width:15px;}</style></defs><line class="a" x1="823.78" y1="1.83" x2="888.78" y2="488.83"/><line class="a" x1="530.78" y1="275.83" x2="823.78" y2="1.83"/><polyline class="a" points="823.78 1.83 685.78 322.83 530.78 275.83"/><line class="a" x1="465.78" y1="275.83" x2="172.78" y2="1.83"/><path class="a" d="M890.5,327.5h-65" transform="translate(-359.72 -51.67)"/><path class="a" d="M858,925.5l32.5-598" transform="translate(-359.72 -51.67)"/><polyline class="a" points="465.78 275.83 310.78 322.83 4.78 570.83 338.78 714.83 657.78 714.83 991.78 570.83 685.78 322.83 498.28 873.83"/><line class="a" x1="498.28" y1="873.83" x2="465.78" y2="275.83"/><line class="a" x1="107.78" y1="488.83" x2="498.28" y2="873.83"/><polyline class="a" points="888.78 488.83 509.78 666.83 486.78 666.83 107.78 488.83"/><line class="a" x1="498.28" y1="873.83" x2="888.78" y2="488.83"/><polyline class="a" points="172.78 1.83 310.78 322.83 498.28 873.83"/><line class="a" x1="107.78" y1="488.83" x2="172.78" y2="1.83"/></svg>
		</a>
	</div>
	<nav>
		<ul>
			<li>
				<a href="search.php">Search</a>
			</li>
			<?php if ($isLoggedIn): ?>
				<li>
					<a href="logout.php">Logout</a>
				</li>
			<?php else: ?>
				<li>
					<a href="register.php">Register</a>
				</li>
				<li>
					<a href="login.php">Login</a>
				</li>
			<?php endif; ?>
		</ul>
	</nav>
	<?php ?>
</header>