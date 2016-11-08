<?php 
// Registration page for generating Weather.IM Accounts
require_once "../config/settings.inc.php";
require_once "../include/myview.php";

$content = null;
do {
if (isset($_POST["agree"]) && $_POST["botq"] == 'iowa'){
	// Start of new account logic!
	$p1 = isset($_POST["p1"]) ? $_POST["p1"]: null;
	$p2 = isset($_POST["p2"]) ? $_POST["p2"]: null;
	$user = isset($_POST["username"]) ? $_POST["username"]: null;
	$email = isset($_POST["email"]) ? $_POST["email"]: null;
	if ($p1 == null || $p2 == null || $user == null || $email == null){
		break;
	}
	if ($p1 != $p2){
		break;
	}
	$url = sprintf("%s?type=add&secret=%s&username=%s".
			"&password=%s&email=%s",
			$config["openfire_userservice_uri"],
			$config["openfire_userservice_secret"],
			urlencode($user), urlencode($p1), urlencode($email));
	$response = file_get_contents($url);
	if ($response != "<result>ok</result>\n"){
		error_log($response);
		break;
	}
	$content = <<<EOF
	<p>Congratulations, your account "$user@weather.im" has been created.
		You can now use a XMPP client to connect to our server or directly
	use the <a href="/live/">Live Application</a></p>
EOF;
}
} while(0);

$t = new MyView();
$t->title = "Account Registration";
if ($content == null){
$t->content = <<<EOF
<h3>Weather.IM XMPP/Jabber Account Registration</h3>

<p>Due to spammers and bots, we need to have you register for an account via
this registration form.  This project won't sell your information or anything
like that.</p>

<form name="new" method="POST">

  <div class="checkbox">
    <label>
      <input type="checkbox" name="agree"> I am at least 13 years of age and
	agree that my usage is without expressed or implied warranty by the
	Weather.IM Project
    </label>
  </div>
 
  <div class="form-group">
    <label for="inputusername">Username</label>
    <input name="username" type="text" class="form-control" id="inputusername" placeholder="Username, no spaces, no @">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input name="p1" type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword2">Password Re-enter</label>
    <input name="p2" type="password" class="form-control" id="exampleInputPassword2" placeholder="Password">
  </div>
  <div class="form-group">
    <label for="exampleInputEmail1">Email address</label>
    <input name="email" type="email" class="form-control" id="exampleInputEmail1" placeholder="Email">
  </div>
  <div class="form-group">
    <label for="botq">I am not a bot, type just "iowa" in the field below</label>
    <input name="botq" type="text" class="form-control" id="botq" placeholder="type iowa">
  </div>
  <button type="submit" class="btn btn-default">Submit</button>
		
		
</form>

EOF;
} else {
	$t->content = $content;
}
$t->render("single.phtml");

?>