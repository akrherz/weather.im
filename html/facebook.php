<?php
/*
 * Front End to an app to save a facebook user's settings to the database
 *
CREATE TABLE iemface_user_details(
    userid bigint UNIQUE,
    registered timestamp with time zone DEFAULT now()
);
GRANT all on iemface_user_details to apache;

CREATE TABLE iemface_user_subscriptions(
    userid bigint,
    channel varchar(24),
    FOREIGN KEY (userid) REFERENCES iemface_user_details(userid)
);
GRANT all on iemface_user_subscriptions to apache;
 */
include("../config/settings.inc.php");
include("$rootpath/include/facebook-platform/php/facebook.php");
$facebook = new Facebook($facebook_appapikey, $facebook_appsecret);
if (isset($DEVEL)){ $user_id = 0; }
else              { $user_id = $facebook->require_login(); }

$has_permission = $facebook->api_client->users_hasAppPermission("publish_stream");
 


include("$rootpath/include/wfo_locs.php");
$postgis = pg_connect("dbname=postgis");
$rs = pg_prepare($postgis, "QUERYFOR", "SELECT * from 
      iemface_user_subscriptions WHERE userid = $1");
$rs = pg_prepare($postgis, "DELETEALL", "DELETE from 
      iemface_user_subscriptions WHERE userid = $1");
$rs = pg_prepare($postgis, "INSERT", "INSERT into 
      iemface_user_subscriptions(userid, channel) VALUES ($1,$2)");
$rs = pg_prepare($postgis, "SELECTUSER", "SELECT * from 
      iemface_user_details WHERe userid = $1");
$rs = pg_prepare($postgis, "INSERTUSER", "INSERT into 
      iemface_user_details(userid) VALUES($1)");

/* Look for our user*/
$rs = pg_execute($postgis, "SELECTUSER", Array($user_id));
if (pg_num_rows($rs) == 0){
  pg_execute($postgis, "INSERTUSER", Array($user_id));
}

$attachment = Array(
 'name' => 'Weather.IM',
 'href' => "http://weather.im/facebook.php",
 'caption' => 'caption',
 'description' => 'Feature Image',
 'media' => array(array('type' => 'image',
    'src' => "http://weather.im/images/bender.jpg",
    'href' => "http://weather.im/facebook.php")),
);
$action_links = array(
  array('text' => 'Permalink',
        'href' => "http://weather.im/facebook.php"));


if (isset($_REQUEST["do"])){
  pg_execute($postgis, "DELETEALL", Array($user_id));

  if (isset($_REQUEST["channels"])){
     $msg = "I subscribed to Forecast Discussion's from offices: ";
     while( list($idx,$channel) = each($_REQUEST["channels"])){
        pg_execute($postgis, "INSERT", Array($user_id, $channel));
        $msg .= " [$channel], ";
     }
     if(!$has_permission){
      //echo "<br /><fb:prompt-permission perms=\"publish_stream\">Allow Posts on your wall!!</fb:prompt-permission>";
      header("Location: http://www.facebook.com/authorize.php?api_key=${facebook_appapikey}&v=1.0&ext_perm=publish_stream");
     } else {
      $facebook->api_client->stream_publish($msg,$attachment,
             $action_links,$user_id,$user_id);
     }
  }
}

$subscriptions = Array();
$rs = pg_execute($postgis, "QUERYFOR", Array($user_id));
for($i=0;$row=@pg_fetch_array($rs,$i);$i++){
  $subscriptions[ $row["channel"] ] = True;
}

include("$rootpath/include/header.php");
?>
<h3>Weather.IM Facebook Gateway</h3>

<div id="warning">This application is a demonstration prototype of the 
automated and configurable relay of a NWS Text Product to Facebook.</div>

<p>Hello Facebook User!  You can configure this form to allow me to post
notifications to your faceback immediately when your favorite National 
Weather Service forecast issues an Area Forecast Discussion.</p>

<form method="POST" name="gogo">
<table><tr><td valign='top'>
<input type="hidden" value="do" name="do">
<?php
$cntr = 0;
while(list($key,$val) = each($wfos)){
  echo sprintf("<input type=\"checkbox\" value=\"AFD%s\" name=\"channels[]\" %s />%s - %s <br />\n",
       $key, (array_key_exists("AFD$key",$subscriptions) ? "CHECKED": ""), 
       $key, $val["city"]);
  $cntr += 1;
  if ($cntr % 50 == 0){ echo "</td><td valign='top'>\n";}
}
?>
</td></tr></table>

<p><input type="submit" value="Make it so #1">
</form>
<?php include("$rootpath/include/footer.php"); ?>
