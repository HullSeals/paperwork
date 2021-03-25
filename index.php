<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../users/init.php';  //make sure this path is correct!
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include '../assets/includes/headerCenter.php'; ?>
    <meta content="Hull Seals Paperwork" name="description">
    <title>Paperwork Portal | The Hull Seals</title>
</head>
<body>
    <div id="home">
      <?php include '../assets/includes/menuCode.php';?>
        <section class="introduction container">
	    <article id="intro3">
        <h1>Case Paperwork</h1>
        <p>SEAL ONLY: To continue, please select your paperwork type.</p>
        <div class="btn-group" style="display:flex;" role="group">
          <a href="seals" class="btn btn-lg btn-primary">Seals</a>
          <a href="fishers" class="btn btn-lg btn-info">Kingfishers</a>
      </div>
      <br>
      <p>Please do not fill out paperwork if you were the client.</p>
      </article>
            <div class="clearfix"></div>
        </section>
    </div>
    <?php include '../assets/includes/footer.php'; ?>
</body>
</html>
