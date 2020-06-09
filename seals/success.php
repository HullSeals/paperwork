<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../../users/init.php'; //make sure this path is correct!
if (!securePage($_SERVER['PHP_SELF']))
{
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<link href="../favicon.ico" rel="icon" type="image/x-icon">
	<link href="../favicon.ico" rel="shortcut icon" type="image/x-icon">
	<meta charset="UTF-8">
	<meta content="Wolfii Namakura" name="author">
	<meta content="hull seals, elite dangerous, distant worlds, seal team fix, mechanics, dw2" name="keywords">
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0" name="viewport">
	<meta content="Welcome to the Hull Seals, Elite Dangerous's Premier Hull Repair Specialists!" name="description">
	<title>Paperwork | The Hull Seals</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js">
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js">
	</script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">
	</script>
	<link href="../styles.css" rel="stylesheet" type="text/css">
	<link href="//cdnpub.websitepolicies.com/lib/cookieconsent/1.0.2/cookieconsent.min.css" rel="stylesheet" type="text/css">
	<script src="//cdnpub.websitepolicies.com/lib/cookieconsent/1.0.2/cookieconsent.min.js">
	</script>
	<script>
	window.addEventListener("load", function () {window.wpcc.init({"colors": {"popup": {"background": "#222222","text": "#ffffff","border": "#bd9851"},"button": {"background": "#bd9851","text": "#000000"}},"border": "thin","corners": "small","padding": "small","margin": "small","transparency": "25","fontsize": "small","content": {"href": "https://hullseals.space/knowledge/books/important-information/page/cookie-policy"}});});
	</script>
	<style>
	.input-group-prepend input[type="checkbox"] {margin-right: 5px;}label {user-select: none;}
	</style>
</head>
<body>
	<div id="home">
		<header>
			<nav class="navbar container navbar-expand-lg navbar-expand-md navbar-dark" role="navigation">
				<a class="navbar-brand" href="../"><img alt="Logo" class="d-inline-block align-top" height="30" src="../images/emblem_scaled.png"> Hull Seals</a><button aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler" data-target="#navbarNav" data-toggle="collapse" type="button"><span class="navbar-toggler-icon"></span></button>
				<div class="collapse navbar-collapse" id="navbarNav">
					<ul class="navbar-nav">
						<li class="nav-item">
							<a class="nav-link" href="../">Home</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="../knowledge">Knowledge Base</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="../journal">Journal Reader</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="../about">About</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="../contact">Contact</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="https://hullseals.space/users/">Login/Register</a>
						</li>
					</ul>
				</div>
			</nav>
		</header>
		<section class="introduction">
			<article>
				<h1>Seal Case Paperwork</h1>
				<h5 class="text-success">Thank you for submitting your paperwork, Seal! You may now close the tab.</h5>
			</article>
		</section>
	</div>
	<footer class="page-footer font-small">
		<div class="container">
			<div class="row">
				<div class="col-md-9 mt-md-0 mt-3">
					<h5 class="text-uppercase">Hull Seals</h5>
					<p><em>The Hull Seals</em> were established in January of 3305, and have begun plans to roll out galaxy-wide!</p><a class="btn btn-sm btn-secondary" href="https://fuelrats.com/i-need-fuel">Need Fuel? Call the Rats!</a>
				</div>
				<hr class="clearfix w-100 d-md-none pb-3">
				<div class="col-md-3 mb-md-0 mb-3">
					<h5 class="text-uppercase">Links</h5>
					<ul class="list-unstyled">
						<li>
							<a href="https://twitter.com/HullSeals" target="_blank"><img alt="Twitter" height="20" src="../images/twitter_loss.png" width="20"></a> <a href="https://reddit.com/r/HullSeals" target="_blank"><img alt="Reddit" height="20" src="../images/reddit.png" width="20"></a> <a href="https://www.youtube.com/channel/UCwKysCkGU_C6V8F2inD8wGQ" target="_blank"><img alt="Youtube" height="20" src="../images/youtube.png" width="20"></a> <a href="https://www.twitch.tv/hullseals" target="_blank"><img alt="Twitch" height="20" src="../images/twitch.png" width="20"></a> <a href="https://gitlab.com/hull-seals-cyberseals" target="_blank"><img alt="GitLab" height="20" src="../images/gitlab.png" width="20"></a>
						</li>
						<li>
							<a href="/donate">Donate</a>
						</li>
						<li>
							<a href="https://hullseals.space/knowledge/books/important-information/page/privacy-policy">Privacy & Cookies Policy</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="footer-copyright">
			Site content copyright © 2019, The Hull Seals. All Rights Reserved. Elite Dangerous and all related marks are trademarks of Frontier Developments Inc.
		</div>
	</footer>
</body>
</html>
