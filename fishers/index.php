<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Declare Title, Content, Author
$pgAuthor = "";
$pgContent = "";
$useIP = 1; //1 if Yes, 0 if No.
$customContent = '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/bootstrap-tokenfield.min.css">
 <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
 <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/bootstrap-tokenfield.js"></script>
 <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/bootstrap-tokenfield.min.css">
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
 <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
 <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js" integrity="sha384-Q9RsZ4GMzjlu4FFkJw4No9Hvvm958HqHmXI9nqo5Np2dA/uOVBvKVxAvlBQrDhk4" crossorigin="anonymous"></script>
';
//UserSpice Required
require_once '../../users/init.php';  //make sure this path is correct!
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

$db = include '../db.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli($db['server'], $db['user'], $db['pass'], $db['db'], $db['port']);

//Get the list of all possible Platforms from centralized Lookup Table.
$platformList = [];
$res = $mysqli->query('SELECT * FROM lookups.platform_lu ORDER BY platform_id');
while ($platformInfo = $res->fetch_assoc())
{
    $platformList[$platformInfo['platform_id']] = $platformInfo['platform_name'];
}

//Get the list of all possible case statuses from centralized Lookup Table.
$statusList = [];
$res = $mysqli->query('SELECT * FROM lookups.status_lu ORDER BY status_id');
while ($casestat2 = $res->fetch_assoc())
{
  if ($casestat2['status_name'] == 'Open') {
    continue;
  }//We don't need to file paperwork on open cases, right now.
  if ($casestat2['status_name'] == 'On Hold') {
    continue;
  }
  if ($casestat2['status_name'] == 'Delete Case') {
    continue;
  }
    $statusList[$casestat2['status_id']] = $casestat2['status_name'];
}

$stmt3 = $mysqli->prepare("SELECT COUNT(seal_name) AS num_cmdrs FROM sealsudb.staff WHERE seal_ID = ? AND del_flag != True");
$stmt3->bind_param("i", $user->data()->id);
$stmt3->execute();
$resultnum = $stmt3->get_result();
$resultnum = $resultnum->fetch_assoc();

//Type of Case. For KFs, only take KF cases (8-11)
$typeList = [];
$res = $mysqli->query('SELECT * FROM lookups.case_color_lu WHERE color_id IN (8, 9 , 10, 11) ORDER BY color_name');
while ($trow = $res->fetch_assoc()) {
    $typeList[$trow['color_id']] = $trow['color_name'];
}

//The good stuff. What happens when we hit submit.
$validationErrors = [];
$data = [];
if (isset($_GET['send'])) {
    foreach ($_REQUEST as $key => $value) {
        $data[$key] = strip_tags(stripslashes(str_replace(["'", '"'], '', $value)));
    } //ensure the data passes first-level validation. We'll do more in the DB.
	    if (strlen($data['client_nm']) > 45) {
        $validationErrors[] = 'commander name too long';
    }
    if (strlen($data['curr_sys']) > 100) {
        $validationErrors[] = 'system too long';
    }
	    if (strlen($data['curr_planet']) > 10) {
        $validationErrors[] = 'planet too long';
    }
    if (strlen($data['curr_coord']) > 20) {
        $validationErrors[] = 'coordinates too long';
    }
	if (!isset($statusList[$data['case_stat']])) {
        $validationErrors[] = 'invalid status';
    }
    if (isset($data['dispatched'])) {
    $data['dispatched'] = isset($data['dispatched']);
    }
      else {
  	    $data['dispatched']=0;
      }

    if (!isset($lgd_ip)) {
        $validationErrors[] = 'invalid IP Address';
    }
    if (!isset($platformList[$data['platypus']])) {
        $validationErrors[] = 'invalid platform';
    }
    if ($data['dispatched'] == 0 && (!isset($data['dispatcher']) || empty($data['dispatcher'])))    {
      $validationErrors[] = 'Please include the Dispatcher!';
    }
    if (!count($validationErrors))
    {
      $stmt = $mysqli->prepare('CALL spTempCreateKFCaseCleaner(?,?,?,?,?,?,?,?,?,?,?,@caseID)');
      $stmt->bind_param('ssssiiiisis', $data['client_nm'], $data['curr_sys'], $data['curr_planet'], $data['curr_coord'], $data['platypus'], $data['case_stat'], $data['case_type'], $data['dispatched'], $data['notes'], $user->data()->id, $lgd_ip);
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
          $thenumber2 = 2;
            $stmt2 = $mysqli->prepare('CALL spCreateCaseAssigned(?,?,?,?)');
            $stmt2->bind_param('isii', $extractArray, $dispNM, $thenumber2, $thenumber1);
            $stmt2->execute();
            $stmt2->close();
        }
        $osarray = explode(", ", $data['other_seals']);
        foreach ($osarray as $osNM)
        {
            $stmt3 = $mysqli->prepare('CALL spCreateCaseAssigned(?,?,?,?)');
            $thenumber2 = 2;
            $stmt3->bind_param('isii', $extractArray, $osNM, $thenumber2, $thenumber2);
            $stmt3->execute();
            $stmt3->close();
        }
        require_once '../shout.php';
        header("Location: success.php");    }
}
?>
              				<h1>Kingfisher Case Paperwork</h1>
              				<h5>Do NOT complete for Self Repairs.</h5>
              				<hr>
                      <?php if (count($validationErrors)) {foreach ($validationErrors as $error) {echo '<div class="alert alert-danger">' . $error . '</div>';}echo '<br>';}
                            if ($resultnum['num_cmdrs'] === 0) { ?>
                              <div class="alert alert-danger" role = "alert"><h2> You cannot file paperwork without a valid CMDR registered.</h2><a href="https://hullseals.space/cmdr-management/" class="alert-link" target="_blank">Click Here</a> to set one, then refresh the page. </div>
                            <?php }
                            else { ?>
              				<form action="?send" method="post">
                        <div class="input-group mb-3">
                        						<p>Your ID has been logged as <?php echo echousername($user->data()->id); ?>. This will be entered as the Lead Fisher.</p><p>Do not enter yourself as either a Dispatcher or another Fisher.</p>
                        					</div>
                                  <div class="input-group mb-3">
                            <input type="text" name="client_nm" value="<?= $data['client_nm'] ?? '' ?>" class="form-control" placeholder="Client Name" aria-label="Client Name" required>
                        </div>
						<div class="input-group mb-3">
                            <input type="text" name="curr_sys" value="<?= $data['curr_sys'] ?? '' ?>" class="form-control" placeholder="System" aria-label="System" required>
                        </div>
						<div class="input-group mb-3">
                            <input type="text" name="curr_planet" value="<?= $data['curr_planet'] ?? '' ?>" class="form-control" placeholder="Planet" aria-label="Planet" required>
                        </div>
						<div class="input-group mb-3">
                            <input type="text" name="curr_coord" value="<?= $data['curr_coord'] ?? '' ?>" class="form-control" placeholder="Coordinates (+/-000.000, +/-000.000)" aria-label="Coordinates" pattern="(\+?|-)\d{1,3}\.\d{3}\,(\+?|-)\d{1,3}\.\d{3}" required>
                        </div>
						<div class="input-group mb-3">
              <div class="input-group-prepend">
              							<span class="input-group-text">Platform</span>
              						</div><select class="custom-select" id="inputGroupSelect01" name="platypus" required="">
              							<?php
              							  foreach ($platformList as $platformId => $platformName) {
              							      echo '<option value="' . $platformId . '"' . ($platformInfo['platypus'] == $platformId ? ' checked' : '') . '>' . $platformName . '</option>';
              							  }
              							  ?>
              						</select>
              					</div>
                        <div class="input-group mb-3">
                        						<div class="input-group-prepend">
                        							<span class="input-group-text">Was the Case Successful?</span>
                        						</div><select class="custom-select" id="inputGroupSelect01" name="case_stat" required="">
                        							<?php
                        							  foreach ($statusList as $statusId => $statusName) {
                        							      echo '<option value="' . $statusId . '"' . ($casestat2['case_stat'] == $statusId ? ' checked' : '') . '>' . $statusName . '</option>';
                        							  }
                        							  ?>
                        						</select>
                        					</div>
                                  <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Case Type?</span>
                            </div>
                            <select name="case_type" class="custom-select" id="inputGroupSelect01" placeholder="Test" required>
                                <?php
                                foreach ($typeList as $typeId => $typeName) {
                                    echo '<option value="' . $typeId . '"' . ($trow['case_type'] == $typeId ? ' checked' : '') . '>' . $typeName . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="input-group mb-3">
                          <label class="input-group-text text-primary" id="dispatched"><input aria-label="Self Dispatched?" name="dispatched" type="checkbox" value="1" data-toggle="toggle" data-on="Self-Dispatched" data-off="Dispatched Case" data-onstyle="danger" data-offstyle="success"></label>
                        </div>
                        <div class="input-group mb-3">
                          <input type="text" name="dispatcher" id="dispatcher" value="<?= $data['dispatcher'] ?? '' ?>" class="form-control" placeholder="Who was Dispatching? (If None, Leave Blank)" aria-label="Who was Dispatching?">
                        </div>
              					<div class="input-group mb-3">
                          <input aria-label="other_seals" type="text" id="other_seals" name="other_seals" placeholder="Other Fishers on the Case? (If None, Leave Blank)" class="form-control" value="<?= $data['other_seals'] ?? '' ?>">
                        </div>
              					<div class="input-group mb-3">
                          <textarea aria-label="Notes (Required)" required minlength="10" class="form-control" name="notes" placeholder="Notes (Required).
                          Suggested notes include:
                          - Distance Traveled
                          - Unique or Unusual details about the rescue
                          - A Case Screenshot Link (Let an Admin know if you want it considered for Twitter!)
                          - Every Kingfisher case is unique - your notes should be too!" rows="5"><?= $data['notes'] ?? '' ?>
</textarea>
                        </div>


                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                    <?php } ?>
                    <?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>
<script type="text/javascript">
  $('#other_seals').tokenfield({
    autocomplete: {
      source: function (request, response) {
          jQuery.get("../fetch.php", {
              query: request.term
          }, function (data) {
              data = $.parseJSON(data);
              response(data);
          });
      },
      delay: 100
    },
  });
</script>
<script type="text/javascript">
  $('#dispatcher').tokenfield({
    autocomplete: {
      source: function (request, response) {
          jQuery.get("../fetch.php", {
              query: request.term
          }, function (data) {
              data = $.parseJSON(data);
              response(data);
          });
      },
      delay: 100
    },
  });
</script>
