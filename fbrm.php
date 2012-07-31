<?php

// Awesome FB APP 

//

// Name: MyAPP 

//

require_once 'facebook.php';// this line calls our facebook.php file 

//that is the core of PHP facebook API



// Create our Application instance.

$facebook = new Facebook(array(

  'appId' => 'YOUR APP ID',

  'secret' => 'YOUR APP SECRET',

  'cookie' => true,

)); // all we are doing is creating an array for facebook to use with our 

//app id and app secret in and setting the cookie to true

try {

  $me = $facebook->api('/me');

} catch (FacebookApiException $e) {

  error_log($e);

} // this code is saying if the session to the app is created use the

// $me as a selector for the information or die

?>