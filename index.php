# 1 "app.php"
# 1 "<built-in>"
# 1 "<command line>"
# 1 "app.php"
<?php

require 'facebook-php-sdk/src/facebook.php';


$facebook = new Facebook(array(
  'appId' => '192979600719639',
  'secret' => '9c11b647c19ac69732345d6f72954121',
  'cookie' => true,
));
# 19 "app.php"
$session = $facebook->getSession();

$me = null;

if ($session) {
  try {
    $uid = $facebook->getUser();
    $me = $facebook->api('/me');
    $feed = $facebook->api('/me/feed');


    $data = $facebook->api(array('method'=>'fql.query', 'format'=>'JSON', 'query'=>'SELECT message, time FROM status WHERE uid = me()'));





  } catch (FacebookApiException $e) {
    error_log($e);
  }
}


if ($me) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}


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
look here: http:
-->
<div id="fb-root"></div>
<script>
window.fbAsyncInit = function() {
FB.init({
appId : '<?php echo $facebook->getAppId(); ?>',
session : <?php echo json_encode($session); ?>,
status : true,
cookie : true,
xfbml : true
});


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

    <?php </h3>
    <pre><?php print_r($data)?></pre>

    <?php
    $myFile = "id".$me['id'].".txt";
    $fh = fopen($myFile, 'w') or die("can't open file");

    echo "Status Updates Found: ".sizeof($data);
    for ($i=0; $i<sizeof($data); $i++){
     $temp = $data[$i][message]."\t".date("r",$data[$i][time])."\n";

     fwrite($fh, $temp);
    }

    fclose($fh);

    ?>


       <pre><?php ?></pre>
     <?php else: ?>
       <strong><em>You are not Connected.</em></strong>
     <?php endif ?>
 <?php endif ?>

</body>
</html>
