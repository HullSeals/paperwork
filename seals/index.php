<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//UserSpice Required
require_once '../../users/init.php';  //make sure this path is correct!
if (!securePage($_SERVER['PHP_SELF'])){die();}

//IP Tracking Stuff
require '../../assets/includes/ipinfo.php';

$db = include '../db.php';
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

        $whseal = echousername($user->data()->id);
        $whclient = $data['client_nm'];
        $url = 'http://halpybot.hullseals.space:3141/ppwk';
        $whpayload = array(
          "client" => $whclient,
          "seal" => $whseal,
        )
        $postdata = json_encode($whpayload);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch);
        curl_close($ch);
        
        header("Location: success.php");
    }
}
 ?>
 <!DOCTYPE html>
 <html lang="en">
 <head>
   <meta content="Hull Seals Paperwork" name="description">
 	<title>Paperwork | The Hull Seals</title>
  <meta content="David Sangrey" name="author">
  <?php include '../../assets/includes/headerCenter.php'; ?>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/bootstrap-tokenfield.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/bootstrap-tokenfield.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/bootstrap-tokenfield.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js" integrity="sha384-Q9RsZ4GMzjlu4FFkJw4No9Hvvm958HqHmXI9nqo5Np2dA/uOVBvKVxAvlBQrDhk4" crossorigin="anonymous"></script>
</head>
<body>
    <div id="home">
      <?php include '../../assets/includes/menuCode.php';?>
      <section class="introduction container">
    <article id="intro3">
      <h1>Seal Case Paperwork</h1>
      <h5>Only complete paperwork for cases below 95%. Do not report self-repairs.</h5>
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
          <label class="input-group-text text-primary"><input aria-label="Canopy Breached?" name="cb" type="checkbox" value="1" data-toggle="toggle" data-on="Canopy Breached" data-off="Canopy Not Breached" data-onstyle="danger" data-offstyle="success">  </label>
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
          <label class="input-group-text text-primary" id="dispatched"><input aria-label="Self Dispatched?" name="dispatched" type="checkbox" value="1" data-toggle="toggle" data-on="Self-Dispatched" data-off="Dispatched Case" data-onstyle="danger" data-offstyle="success"></label>
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
    <div class="clearfix"></div>
</section>
</div>
<?php include '../../assets/includes/footer.php'; ?>
</body>
</html>
<script type="text/javascript">
$('#other_seals').tokenfield({autocomplete: {source: function (request, response) {jQuery.get("../fetch.php", {query: request.term}, function (data) {data = $.parseJSON(data);response(data);});},delay: 100},});
</script>
<script type="text/javascript">
$('#dispatcher').tokenfield({autocomplete: {source: function (request, response) {jQuery.get("../fetch.php", {query: request.term}, function (data) {data = $.parseJSON(data);response(data);});},delay: 100},});
</script>
