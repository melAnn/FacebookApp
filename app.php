<?php

require 'facebook-php-sdk/src/facebook.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => '192979600719639',
  'secret' => '9c11b647c19ac69732345d6f72954121',
  'cookie' => true,
));

// We may or may not have this data based on a $_GET or $_COOKIE based session.
//
// If we get a session here, it means we found a correctly signed session using
// the Application Secret only Facebook and the Application know. We dont know
// if it is still valid until we make an API call using the session. A session
// can become invalid if it has already expired (should not be getting the
// session back in this case) or if the user logged out of Facebook.
$session = $facebook->getSession();

$me = null;
// Session based API call.
if ($session) {
  try {
    $uid = $facebook->getUser();
    $me = $facebook->api('/me');
    $feed = $facebook->api('/me/feed');
    
    //$query = "SELECT name FROM user WHERE uid = me()";
    $data = $facebook->api(array('method'=>'fql.query', 'format'=>'JSON', 'query'=>'SELECT message, time FROM status WHERE uid = me()'));
    //599011492
     
    //$data = $facebook->api(array('method'=>'fql.query', 'query'=>'SELECT text FROM comment WHERE postid = 1200943_10100205487697813'));
    //$data = $facebook->api('/search?q=1200943&type=user');
    
  } catch (FacebookApiException $e) {
    error_log($e);
  }
}

// login or logout url will be needed depending on current user state.
if ($me) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}

// This call will always work since we are fetching public data.
$naitik = $facebook->api('/naitik');

?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title>Social Study</title>
<style>
body {
font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
}
h1 a {
text-decoration: none;
color: #3b5998;
}
h1 a:hover {
text-decoration: underline;
}
</style>
</head>
<body>
<!--
We use the JS SDK to provide a richer user experience. For more info,
look here: http://github.com/facebook/connect-js
-->
<div id="fb-root"></div>
<script>
window.fbAsyncInit = function() {
FB.init({
appId : '<?php echo $facebook->getAppId(); ?>',
session : <?php echo json_encode($session); ?>, // don't refetch the session when PHP already has it
status : true, // check login status
cookie : true, // enable cookies to allow the server to access the session
xfbml : true // parse XFBML
});

// whenever the user logs in, we refresh the page
	FB.Event.subscribe('auth.login', function() {
		window.location.reload();
		});
	};

	(function() {
		var e = document.createElement('script');
		e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
		e.async = true;
		document.getElementById('fb-root').appendChild(e);
	}());
</script>

	<?php if ($me and !$feed): ?>
	<h1>To participate study, click the button below and then click 'ALLOW' </h1>  
   	<fb:login-button perms="read_stream">
       	Continue
   	</fb:login-button>
	<?php endif ?>

	<?php if ($me): ?>
		<a href="<?php echo $logoutUrl; ?>">
		<img src="http://static.ak.fbcdn.net/rsrc.php/z2Y31/hash/cxrz4k7j.gif">
		</a>
	<?php else: ?>
	<h2> Welcome to Comm 168 Social Studies Research Application </h2>
	<h3>To participate study, login and then click 'ALLOW' </h3>
		<div>
			Login Here: <fb:login-button perms="read_stream"></fb:login-button>
		</div>
	<?php endif ?>

	<?php if ($me): ?>
	    <?php if ($feed): ?>
	    		<h3>Thank you for participating, <?php echo $me['first_name']; ?>! <br></br>
				
				<?php /*echo $me['first_name']; ?>'s newsfeed:*/ </h3>
				<pre><?php print_r($data)?></pre>
				
				<?php //write to a file
				$myFile = "id".$me['id'].".txt";
				$fh = fopen($myFile, 'w') or die("can't open file");
				
				echo "Status Updates Found: ".sizeof($data);
				for ($i=0; $i<sizeof($data); $i++){
					$temp = $data[$i][message]."\t".date("r",$data[$i][time])."\n";
					//echo $temp;
					fwrite($fh, $temp);
				}
				
				fclose($fh);
								
				?>
				
				
	    		<pre><?php /*print_r($feed);*/ ?></pre>
	    <?php else: ?>
	    		<strong><em>You are not Connected.</em></strong>
	    <?php endif ?>
	<?php endif ?>

</body>
</html>
