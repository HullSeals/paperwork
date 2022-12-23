<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Declare Title, Content, Author
$pgAuthor = "David Sangrey";
$pgContent = "File Seal or Fisher Paperwork";
$useIP = 0; //1 if Yes, 0 if No.

//UserSpice Required
require_once '../users/init.php';  //make sure this path is correct!
require_once $abs_us_root . $us_url_root . 'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])) {
  die();
}
?>
<h1>Case Paperwork</h1>
<?php
if (isset($_GET['id'])) {
  sessionValMessages(
    "",
    "Paperwork successfully filed for case " . $_GET['id'],
  );
}
if (isset($_GET['type']) && $_GET['type'] == "seal") { ?>
  <div class="alert alert-success" role="alert">
    <h5>Thank you for submitting your paperwork, Seal! You may now close the tab.</h5>
  </div>
  <hr>
  <p>Need to submit another case?</p>
<?php } elseif (isset($_GET['type']) && $_GET['type'] == "fisher") { ?>
  <div class="alert alert-success" role="alert">
    <h5>Thank you for submitting your paperwork, Fisher! You may now close the tab.</h5>
  </div>
  <hr>
  <p>Need to submit another case?</p>
<?php
}
?>
<p>SEAL ONLY: To continue, please select your paperwork type.</p>
<div class="btn-group" style="display:flex;" role="group">
  <a href="seals" class="btn btn-lg btn-primary">Seals</a>
  <a href="fishers" class="btn btn-lg btn-info">Kingfishers</a>
</div>
<br>
<p>Please do not fill out paperwork if you were the client.</p>
<?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>