<?php

require '../include/src/facebook.php';

$con = mysql_connect("localhost:3306","fbremind_sid","gopens66");
mysql_select_db("fbremind_data", $con);


$facebook = new Facebook(array(
  'appId'  => '417146754976486',
  'secret' => '708076e9690f1017c2835378d42a5a3c',
));

// See if there is a user from a cookie
$user = $facebook->getUser();
// Get the current access token
$access_token = $facebook->getAccessToken();

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
    $email = $user_profile['email'];
    $user_id = $user_profile['id'];

    $sql = "SELECT * FROM user where user_id='$user_id'";
	$result = mysql_query($sql);
	$found = false;

	while($row = mysql_fetch_array($result))
	{
	  $found = true;
	}

	if(!$found) {
	  echo "user not found, inserting";
	  $sql = "insert into user values ($user_id,'$email')";
	  $result = mysql_query($sql);
	}

	$sql = "SELECT * FROM user where user_id='$user_id' ";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result))
	{
	  $user_id = $row['user_id'];
	}

	// echo "user id = $user_id";



    // echo '<pre>'.htmlspecialchars(print_r($facebook, true)).'</pre>';
  } catch (FacebookApiException $e) {
    // echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
    $user_profile = null;
  }
}

?>
<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
	<style type="text/css">
	h1 {font-size: 20; font-family: Verdana; color: black; font-style: bold}
	h3 {font-size: 18; font-family: Verdana; color: blue;}
	item {font-size: 14; font-family: Verdana; color: red}
	p {font-size: 20; font-family: Verdana; color: black}
	body {background-color: white}
	input {font-size: 16; font-family: Verdana; color: black}
	td {font-size: 14; font-family: Verdana; color: white}
	textarea { font-family: Verdana; resize:none }

	</style>
  <body>
    <?php if ($user_profile) { ?>
      <?php

	  if(!$con) {
	    echo "<h3>Error: Could not connect to database.</h3>";
	  }
	  else {
	    $email = $user_profile['email'];
	    $first_name = $user_profile['first_name'];
	    $last_name = $user_profile['last_name'];

	    echo "<h1>Welcome, ${first_name} ${last_name}!</h1>";
	    echo "<br/>";
	    echo "<fb:login-button autologoutlink=\"true\" perms=\"email,user_birthday,status_update,publish_stream\"></fb:login-button>";
	    echo "<br/>";

	    $sql = "SELECT * FROM user where email='$email' ";
	    $result = mysql_query($sql);
	    $found = false;
	    $hasgoals = false;

	    while($row = mysql_fetch_array($result))
		{
		  $found = true;
		}

        $sql = "SELECT * FROM goal where user_id=$user_id and status=0";
        $result = mysql_query($sql);

        while($row = mysql_fetch_array($result))
		{
		  $name=$row['name'];
		  $desc=$row['desc'];
		  $start=$row['start'];
		  $end=$row['end'];
		  echo "$name<br/>$desc<br/>$start<br/>$end";
		  $hasgoals = true;
		}


        if($found && !$hasgoals) {
          if($_POST['name']) {
		    echo "goal entered!";
		  	$name =$_POST['name'];
		    $desc =$_POST['desc'];
		    $start=$_POST['start'];
		    $end=$_POST['end'];

		    //echo "$name,$desc,$start,$end";
		    $sql = "insert into goal values ($user_id,null,'$name','$desc','$start','$end',0,now(),now())";
		    //echo "<br/>$sql";
	 	    $result = mysql_query($sql);

	 	    if(!$result){
	 	      echo mysql_error();
	 	    }
	 	  }
	 	  else {
	 	    echo "<form action=\"success.php\" method=post>
			             <h3>Enter a goal!</h3>
			             <item>Name:  </item><input type=text name=\"name\"/><br/>
			             <item>Description:</item><br/><textarea maxlength=200 name=\"desc\" rows=6 cols=50></textarea><br/>
			             <item>Start: </item><input type=date name=\"start\"/><br/>
			             <item>End:   </item><input type=date name=\"end\"/><br/>
             <input type=submit value=\"Submit\"/></form>";
          }
	 	}
      }
      mysql_close($con);
      //echo "<br/>db conn closed";

      ?>










    <?php } else { ?>
      <fb:login-button></fb:login-button>
    <?php } ?>
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId: '<?php echo $facebook->getAppID() ?>',
          cookie: true,
          xfbml: true,
          oauth: true
        });
        FB.Event.subscribe('auth.login', function(response) {
          window.location.reload();
        });
        FB.Event.subscribe('auth.logout', function(response) {
          window.location.reload();
        });
      };
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
  </body>
</html>