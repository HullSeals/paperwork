<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../../users/init.php'; //make sure this path is correct!
if (!securePage($_SERVER['PHP_SELF']))
{
    die();
}
$logged_in = $user->data();
$ip = 'Unable To Log';
$cloudflareIPRanges = array(
    '204.93.240.0/24',
    '204.93.177.0/24',
    '199.27.128.0/21',
    '173.245.48.0/20',
    '103.21.244.0/22',
    '103.22.200.0/22',
    '103.31.4.0/22',
    '141.101.64.0/18',
    '108.162.192.0/18',
    '190.93.240.0/20',
    '188.114.96.0/20',
    '197.234.240.0/22',
    '198.41.128.0/17',
    '162.158.0.0/15'
);

//NA by default.
$ip = 'NA';

//Check to see if the CF-Connecting-IP header exists.
if (isset($_SERVER["HTTP_CF_CONNECTING_IP"]))
{

    //Assume that the request is invalid unless proven otherwise.
    $validCFRequest = false;

    //Make sure that the request came via Cloudflare.
    foreach ($cloudflareIPRanges as $range)
    {
        //Use the ip_in_range function from Joomla.
        if (ip_in_range($_SERVER['REMOTE_ADDR'], $range))
        {
            //IP is valid. Belongs to Cloudflare.
            $validCFRequest = true;
            break;
        }
    }

    //If it's a valid Cloudflare request
    if ($validCFRequest)
    {
        //Use the CF-Connecting-IP header.
        $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    else
    {
        //If it isn't valid, then use REMOTE_ADDR.
        $ip = $_SERVER['REMOTE_ADDR'];
    }

}
else
{
    //Otherwise, use REMOTE_ADDR.
    $ip = $_SERVER['REMOTE_ADDR'];
}

//Define it as a constant so that we can
//reference it throughout the app.
define('IP_ADDRESS', $ip);

//$lgd_ip='notLogged';
$lgd_ip = $ip;
$db = include 'db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli($db['server'], $db['user'], $db['pass'], $db['db'], $db['port']);
$platformList = [];
$res = $mysqli->query('SELECT * FROM lookups.platform_lu ORDER BY platform_id');
while ($burgerking = $res->fetch_assoc())
{
    $platformList[$burgerking['platform_id']] = $burgerking['platform_name'];
}

$statusList = [];
$res = $mysqli->query('SELECT * FROM lookups.status_lu ORDER BY status_id');
while ($casestat2 = $res->fetch_assoc())
{
  if ($casestat2['status_name'] == 'Open') {
    continue;
}
if ($casestat2['status_name'] == 'On Hold') {
  continue;
}

    $statusList[$casestat2['status_id']] = $casestat2['status_name'];
}

$validationErrors = [];
$data = [];
if (isset($_GET['send']))
{
    foreach ($_REQUEST as $key => $value)
    {
        $data[$key] = strip_tags(stripslashes(str_replace(["'", '"'], '', $value)));
    }
    if (strlen($data['client_nm']) > 45)
    {
        $validationErrors[] = 'commander name too long';
    }
    if (strlen($data['curr_sys']) > 100)
    {
        $validationErrors[] = 'system too long';
    }
    $data['hull'] = (int)$data['hull'];
    if ($data['hull'] > 100 || $data['hull'] < 1)
    {
        $validationErrors[] = 'invalid hull';
    }
    $data['cb'] = isset($data['cb']);
    $data['dispatched'] = isset($data['dispatched']);
    if (!isset($platformList[$data['platypus']]))
    {
        $validationErrors[] = 'invalid platform';
    }
    if (!isset($statusList[$data['case_stat']]))
    {
        $validationErrors[] = 'invalid status';
    }
    if (!isset($lgd_ip))
    {
        $validationErrors[] = 'invalid IP Address';
    }
    if (!count($validationErrors))
    {
        $stmt = $mysqli->prepare('CALL spTempCreateHSCaseCleaner(?,?,?,?,?,?,?,?,?,?,@caseID)');
        $stmt->bind_param('ssiiiiisis', $data['client_nm'], $data['curr_sys'], $data['hull'], $data['cb'], $data['platypus'], $data['case_stat'], $data['dispatched'], $data['notes'], $user->data()->id, $lgd_ip);
        $stmt->execute();
        foreach ($stmt->error_list as $error)
        {
            $validationErrors[] = 'DB: ' . $error['error'];
        }
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_array($result, MYSQLI_NUM))
        {
            foreach ($row as $r)
            {
                $extractArray = $r;
            }
        }
        $stmt->close();
        $disparray = explode(", ", $data['dispatcher']);
        foreach ($disparray as $dispNM)
        {
          $thenumber1 = 1;
            $stmt2 = $mysqli->prepare('CALL spCreateCaseAssigned(?,?,?,?)');
            $stmt2->bind_param('isii', $extractArray, $dispNM, $thenumber1, $thenumber1);
            $stmt2->execute();
            $stmt2->close();
        }
        $osarray = explode(", ", $data['other_seals']);
        foreach ($osarray as $osNM)
        {
            $stmt3 = $mysqli->prepare('CALL spCreateCaseAssigned(?,?,?,?)');
            $thenumber1 = 1;
            $thenumber2 = 2;
            $stmt3->bind_param('isii', $extractArray, $osNM, $thenumber1, $thenumber2);
            $stmt3->execute();
            $stmt3->close();
        }
        header("Location: success.php");
    }
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
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/bootstrap-tokenfield.min.css">
  <link rel="stylesheet" type="text/css" href="https://hullseals.space/assets/css/allPages.css" />
  <script src="https://hullseals.space/assets/javascript/allPages.js" integrity="sha384-PsQdnKGi+BdHoxLI6v+pi6WowfGtnraU6GlDD4Uh5Qw2ZFiDD4eWNTNG9+bHL3kf" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/bootstrap-tokenfield.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/bootstrap-tokenfield.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<style>
	.input-group-prepend input[type="checkbox"] {margin-right: 5px;}label {user-select: none;}
	</style>
</head>
<body>
	<div id="home">
		<header>
			<nav class="navbar container navbar-expand-lg navbar-expand-md navbar-dark" role="navigation">
				<a class="navbar-brand" href="../"><img alt="Logo" class="d-inline-block align-top" height="30" src="../images/emblem_scaled.png"> Hull Seals</a> <button aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler" data-target="#navbarNav" data-toggle="collapse" type="button"><span class="navbar-toggler-icon"></span></button>
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
				<h5>Complete for cases below 95%. Do NOT complete for Self Repairs.</h5>
				<hr>
				<?php if (count($validationErrors)) {foreach ($validationErrors as $error) {echo '<div class="alert alert-danger">' . $error . '</div>';}echo '<br>';}?>
				<form action="?send" method="post">
					<div class="input-group mb-3">
						<p>Your ID has been logged as <?php echo echousername($user->data()->id); ?>. This will be entered as the Lead Seal.</p>
						<p>Do not enter yourself as either a Dispatcher or another Seal.</p>
					</div>
					<div class="input-group mb-3">
						<input aria-label="Client Name" class="form-control" name="client_nm" placeholder="Client Name" required="" type="text" value="<?= $data['client_nm'] ?? '' ?>">
					</div>
					<div class="input-group mb-3">
						<input aria-label="System" class="form-control" name="curr_sys" placeholder="System" required="" type="text" value="<?= $data['curr_sys'] ?? '' ?>">
					</div>
					<div class="input-group mb-3">
						<input aria-label="Starting Hull %" class="form-control" max="100" min="1" name="hull" placeholder="Starting Hull %" required="" type="number" value="<?= $data['hull'] ?? '' ?>">
					</div>
					<div class="input-group mb-3">
						<label class="input-group-text text-danger" id="cb"><input aria-label="Canopy Breached?" name="cb" type="checkbox" value="1"> Canopy Breached?</label>
					</div>
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text">Platform</span>
						</div><select class="custom-select" id="inputGroupSelect01" name="platypus" required="">
							<?php foreach ($platformList as $platformId => $platformName) {echo '<option value="' . $platformId . '"' . ($burgerking['platypus'] == $platformId ? ' checked' : '') . '>' . $platformName . '</option>';}?>
						</select>
					</div>
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text">Was the Case Successful?</span>
						</div><select class="custom-select" id="inputGroupSelect01" name="case_stat" required="">
							<?php foreach ($statusList as $statusId => $statusName) {echo '<option value="' . $statusId . '"' . ($casestat2['case_stat'] == $statusId ? ' checked' : '') . '>' . $statusName . '</option>';}?>
						</select>
					</div>
					<div class="input-group mb-3">
						<label class="input-group-text text-primary" id="dispatched"><input aria-label="Self Dispatched?" name="dispatched" type="checkbox" value="1"> Check If Self Dispatched</label>
					</div>
					<div class="input-group mb-3">
						<input aria-label="Who was Dispatching?" class="form-control" id="dispatcher" name="dispatcher" placeholder="Who was Dispatching? (If None, Leave Blank)" type="text" value="<?= $data['dispatcher'] ?? '' ?>">
					</div>
					<div class="input-group mb-3">
						<input aria-label="other_seals" class="form-control" id="other_seals" name="other_seals" placeholder="Other Seals on the Case? (If None, Leave Blank)" type="text" value="<?= $data['other_seals'] ?? '' ?>">
					</div>
					<div class="input-group mb-3">
						<textarea aria-label="Notes (optional)" class="form-control" name="notes" placeholder="Notes (optional)" rows="4"><?= $data['notes'] ?? '' ?>
</textarea>
					</div><button class="btn btn-primary" type="submit">Submit</button>
				</form>
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
			Site content copyright © 2019, The Hull Seals. All Rights Reserved. Elite Dangerous and all related marks are trademarks of Frontier Developments Inc. <span class="float-right pr-3" title="Your IP might be logged for security reasons"><img alt="IP" height="16" src="ip-icon.png"> Logged - <?php echo $ip ?></span>
		</div>
	</footer>
	<script type="text/javascript">
	$('#other_seals').tokenfield({autocomplete: {source: function (request, response) {jQuery.get("fetch.php", {query: request.term}, function (data) {data = $.parseJSON(data);response(data);});},delay: 100},});
	</script>
	<script type="text/javascript">
	$('#dispatcher').tokenfield({autocomplete: {source: function (request, response) {jQuery.get("fetch.php", {query: request.term}, function (data) {data = $.parseJSON(data);response(data);});},delay: 100},});
	</script>
</body>
</html>
