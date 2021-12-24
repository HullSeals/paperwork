<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Declare Title, Content, Author
$pgAuthor = "";
$pgContent = "";
$useIP = 0; //1 if Yes, 0 if No.

//If you have any custom scripts, CSS, etc, you MUST declare them here.
//They will be inserted at the bottom of the <head> section.
$customContent = '<!-- Your Content Here -->';

//UserSpice Required
require_once '../users/init.php';  //make sure this path is correct!
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>
        <h1>Case Paperwork</h1>
        <p>SEAL ONLY: To continue, please select your paperwork type.</p>
        <div class="btn-group" style="display:flex;" role="group">
          <a href="seals" class="btn btn-lg btn-primary">Seals</a>
          <a href="fishers" class="btn btn-lg btn-info">Kingfishers</a>
      </div>
      <br>
      <p>Please do not fill out paperwork if you were the client.</p>
      <?php require_once $abs_us_root . $us_url_root . 'users/includes/html_footer.php'; ?>
