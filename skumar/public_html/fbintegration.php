#!/usr/bin/php

<html>
<head>
<style type="text/css">
h1 {font-size: 24; font-family: Verdana; color: red; font-style: bold}
h3 {font-size: 20; font-family: Verdana; color: blue; font-style: bold}
h5 {font-size: 20; font-family: Verdana; color: black}
p {font-size: 20; font-family: Verdana; color: black}
body {background-color: white}
input {font-size: 16; font-family: Verdana; color: black}
td {font-size: 14; font-family: Verdana; color: white}
textarea { resize:none }
</style>


</head>

<body>
<center>
<h1>Facebook integration</h1>

<?php

require '../src/facebook.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => '417146754976486',
  'secret' => '708076e9690f1017c2835378d42a5a3c',
));



$user = $facebook->getUser();



if ($user) {
  try {
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}


if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}




?>


</center>


</body>



</html>
