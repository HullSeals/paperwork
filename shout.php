<?php
//Authenticaton Info
$auth = require_once 'auth.php';
$key = $auth['key'];
$constant = $auth['constant'];
$webhookurl = $auth['discord'];
$url = $auth['url'];
//Content
$whseal = echousername($user->data()->id);
$whclient = $data['client_nm'];
// IRC Message
$data = [
	"type" => "PPWK",
	"parameters" => [
		"CMDR" => $whclient,
		"Seal" => $whseal
  ]
];
$postdata = json_encode($data);
$hmacdata = preg_replace("/\s+/", "", $postdata);
$auth = hash_hmac('sha256', $hmacdata, $key);
$keyCheck = hash_hmac('sha256', $constant, $key);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'hmac: '. $auth,
	'keyCheck: '. $keyCheck
));
$result = curl_exec($ch);
curl_close($ch);
//Discord Webhook
    $timestamp = date("c", strtotime("now"));
      $json_data = json_encode([
          "content" => "Paperwork Completed",
          "username" => "HalpyBOT",
          "avatar_url" => "https://hullseals.space/images/emblem_mid.png",
          "tts" => false,
          "embeds" => [
              [
                  "title" => "Paperwork Complete",
                  "type" => "rich",
                  "timestamp" => $timestamp,
                  "color" => hexdec( "F5921F" ),
                  "footer" => [
                      "text" => "Hull Seals Case Notification System",
                      "icon_url" => "https://hullseals.space/images/emblem_mid.png"
                  ],
                  "fields" => [
                      [
                          "name" => "Paperwork for case",
                          "value" => $whclient,
                          "inline" => true
                      ],
                      [
                          "name" => "Completed by ",
                          "value" => $whseal,
                          "inline" => true
                      ]
                  ]
              ]
          ]

      ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
$ch = curl_init( $webhookurl );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec( $ch );
curl_close( $ch );
?>
