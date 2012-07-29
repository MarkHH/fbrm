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
<script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({appId: '417146754976486', status: true, cookie: true, xfbml: true});

                /* All the events registered */
                FB.Event.subscribe('auth.login', function(response) {
                    // do something with response
                    login();
                });
                FB.Event.subscribe('auth.logout', function(response) {
                    // do something with response
                    logout();
                });

                FB.getLoginStatus(function(response) {
                    if (response.session) {
                        // logged in and connected user, someone you know
                        FB.Auth.setSession(response.session);
                        login();
                    }
                });
            };
            (function() {
                var e = document.createElement('script');
                e.type = 'text/javascript';
                e.src = document.location.protocol +
                    '//connect.facebook.net/en_US/all.js';
                e.async = true;
                document.getElementById('fb-root').appendChild(e);
            }());

            function login(){
                FB.api('/me', function(response) {
                    document.getElementById('login').style.display = "block";
                    document.getElementById('login').innerHTML = response.name + " succsessfully logged in!";
                });
            }
            function logout(){
                document.getElementById('login').style.display = "none";
            }

            //stream publish method
            function streamPublish(name, description, hrefTitle, hrefLink, userPrompt){
                FB.ui(
                {
                    method: 'stream.publish',
                    message: '',
                    attachment: {
                        name: name,
                        caption: '',
                        description: (description),
                        href: hrefLink
                    },
                    action_links: [
                        { text: hrefTitle, href: hrefLink }
                    ],
                    user_prompt_message: userPrompt
                },
                function(response) {

                });

            }
            function showStream(){
                FB.api('/me', function(response) {
                    //console.log(response.id);
                    streamPublish(response.name, 'Testing the publish on FB', 'hrefTitle', 'http://fbremindme.com', "Share fbremindme");
                });
            }

            function share(){
                var share = {
                    method: 'stream.share',
                    u: 'http://fbremindme.com/'
                };

                FB.ui(share, function(response) { console.log(response); });
            }

            function graphStreamPublish(){
                var body = 'Testing publishing from JavaScript';
                FB.api('/me/feed', 'post', { message: body }, function(response) {
                    if (!response || response.error) {
                        alert('Error occured');
                    } else {
                        alert('Post ID: ' + response.id);
                    }
                });
            }

            function fqlQuery(){
                FB.api('/me', function(response) {
                     var query = FB.Data.query('select name, hometown_location, sex, pic_square from user where uid={0}', response.id);
                     query.wait(function(rows) {

                       document.getElementById('name').innerHTML =
                         'Your name: ' + rows[0].name + "<br />" +
                         '<img src="' + rows[0].pic_square + '" alt="" />' + "<br />";
                     });
                });
            }

            function setStatus(){
                status1 = document.getElementById('status').value;
                FB.api(
                  {
                    method: 'status.set',
                    status: status1
                  },
                  function(response) {
                    if (response == 0){
                        alert('Your facebook status not updated. Give Status Update Permission.');
                    }
                    else{
                        alert('Your facebook status updated');
                    }
                  }
                );
            }
        </script>
<center>
<h1>Retrieve goals</h1>

<?php
require '../include/src/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '417146754976486',
  'secret' => '708076e9690f1017c2835378d42a5a3c',
));

$user = $facebook->getUser();

if($user) {
  echo $user;
}


$valid = 1;

$email = strtoupper($_POST["email"]);

if( trim($email) == "" ) {
  $valid = 0;
}

if( $valid == 0 ) {
  echo "<h3>Error: Please enter proper email.</h3>";
  die("");
}

$con = mysql_connect("localhost:3306","fbremind_sid","gopens66");

if(!$con) {
  echo "<h3>Error: Could not connect to database.</h3>";
}
else {
  mysql_select_db("fbremind_data", $con);
  $sql = "SELECT * FROM remind_me where upper(email)='$email' ";
  $result = mysql_query($sql);
  $found = false;

  while($row = mysql_fetch_array($result))
  {
    echo "<h3>Welcome " . $row['email'] . "</h3>";
    echo "<h3>Goal: " . $row['goal'] . "</h3>";
    echo "<h3>End Date: " . $row['end_date'] . "</h3>";
    echo "<h3>Details: " . $row['details'] . "</h3>";

    $found = true;
  }

  if( !$found ) {
    echo "<h3>Error: Did not find any goals.</h3>";
    die("");
  }



  mysql_close($con);
}



?>


</center>


</body>



</html>
