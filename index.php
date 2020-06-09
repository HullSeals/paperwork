<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../users/init.php';  //make sure this path is correct!
if (!securePage($_SERVER['PHP_SELF'])){die();} 

$ip='Unable To Log';
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
if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
    
    //Assume that the request is invalid unless proven otherwise.
    $validCFRequest = false;
    
    //Make sure that the request came via Cloudflare.
    foreach($cloudflareIPRanges as $range){
        //Use the ip_in_range function from Joomla.
        if(ip_in_range($_SERVER['REMOTE_ADDR'], $range)) {
            //IP is valid. Belongs to Cloudflare.
            $validCFRequest = true;
            break;
        }
    }
    
    //If it's a valid Cloudflare request
    if($validCFRequest){
        //Use the CF-Connecting-IP header.
        $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
    } else{
        //If it isn't valid, then use REMOTE_ADDR. 
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
} else{
    //Otherwise, use REMOTE_ADDR.
    $ip = $_SERVER['REMOTE_ADDR'];
}
 
//Define it as a constant so that we can
//reference it throughout the app.
define('IP_ADDRESS', $ip);

//$lgd_ip='notLogged';
$lgd_ip=$ip;
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

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <link href="../styles.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" type="text/css" href="//cdnpub.websitepolicies.com/lib/cookieconsent/1.0.2/cookieconsent.min.css" />
        <script src="//cdnpub.websitepolicies.com/lib/cookieconsent/1.0.2/cookieconsent.min.js"></script>
        <script>
            window.addEventListener("load", function () {
                window.wpcc.init({
                    "colors": {
                        "popup": {
                            "background": "#222222",
                            "text": "#ffffff",
                            "border": "#bd9851"
                        },
                        "button": {
                            "background": "#bd9851",
                            "text": "#000000"
                        }
                    },
                    "border": "thin",
                    "corners": "small",
                    "padding": "small",
                    "margin": "small",
                    "transparency": "25",
                    "fontsize": "small",
                    "content": {
                        "href": "https://hullseals.space/knowledge/books/important-information/page/cookie-policy"
                    }
                });
            });
        </script>
        <style>
            .input-group-prepend input[type="checkbox"] {
                margin-right: 5px;
            }
            label {
                user-select: none;
            }
        </style>
    </head>

    <body>
        <div id="home">
            <header>
                <nav class="navbar container navbar-expand-lg navbar-expand-md navbar-dark" role="navigation">
                    <a class="navbar-brand" href="../"><img src="../images/emblem_scaled.png" height="30" class="d-inline-block align-top" alt="Logo"> Hull Seals</a>

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

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
                    <h1>Case Paperwork</h1>
					<h5>Please choose the Correct Form.</h5>
					<hr />
					<p>
					<a href="seals" class="btn btn-primary btn-lg">Hull Seals</a>     
					<a href="fishers" class="btn btn-info btn-lg">Kingfishers</a>
					</p>
                </article>
            </section>
        </div>
        <footer class="page-footer font-small">
            <div class="container">
                <div class="row">
                    <div class="col-md-9 mt-md-0 mt-3">
                        <h5 class="text-uppercase">Hull Seals</h5>
                        <p><em>The Hull Seals</em> were established in January of 3305, and have begun plans to roll out galaxy-wide!</p>
                        <a href="https://fuelrats.com/i-need-fuel" class="btn btn-sm btn-secondary">Need Fuel? Call the Rats!</a>
                    </div>
                    <hr class="clearfix w-100 d-md-none pb-3">
                    <div class="col-md-3 mb-md-0 mb-3">
                        <h5 class="text-uppercase">Links</h5>

                        <ul class="list-unstyled">
                            <li><a href="https://twitter.com/HullSeals" target="_blank"><img alt="Twitter" height="20" src="../images/twitter_loss.png" width="20"></a> <a href="https://reddit.com/r/HullSeals" target="_blank"><img alt="Reddit" height="20" src="../images/reddit.png" width="20"></a> <a href="https://www.youtube.com/channel/UCwKysCkGU_C6V8F2inD8wGQ" target="_blank"><img alt="Youtube" height="20" src="../images/youtube.png" width="20"></a> <a href="https://www.twitch.tv/hullseals" target="_blank"><img alt="Twitch" height="20" src="../images/twitch.png" width="20"></a> <a href="https://gitlab.com/hull-seals-cyberseals" target="_blank"><img alt="GitLab" height="20" src="../images/gitlab.png" width="20"></a></li>
                            <li><a href="/donate">Donate</a></li>
                            <li><a href="https://hullseals.space/knowledge/books/important-information/page/privacy-policy">Privacy & Cookies Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-copyright">
                Site content copyright © 2019, The Hull Seals. All Rights Reserved. Elite Dangerous and all related marks are trademarks of Frontier Developments Inc. <span class="float-right pr-3" title="Your IP might be logged for security reasons"><img src="ip-icon.png" witdh="16" height="16" alt="IP"/> Logged - <?php echo $ip ?></span>
            </div>
        </footer>
    </body>

</html>
