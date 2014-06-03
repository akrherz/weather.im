<?php
date_default_timezone_set('America/Chicago');

$id = isset($_GET['id']) ? intval($_GET['id']) : "";
$pid = isset($_GET['pid']) ? substr($_GET['pid'],0,32) : "";
if ($id == "" && $pid == "") die();

if ($id != "") {
  die("Sorry, this interface is no longer available!  Please contact us if you need it back!");
} else {
  // 201212100547-KTOP-FXUS63-AFDTOP
  $pil = substr($pid,25,6);
  $e = substr($pid,0,12);

  $pgconn = pg_connect("dbname=afos host=iemdb user=nobody");
  $rs = pg_prepare($pgconn, "_LSELECT", "SELECT data, 
                                entered at time zone 'UTC' as mytime, source from products
                 WHERE pil = $1 and entered between $2 and $3
                 ORDER by entered ASC LIMIT 100");
  $ts = gmmktime( substr($e,8,2), substr($e,10,2), 0,
  		substr($e,4,2), substr($e,6,2), substr($e,0,4) );
  $offset0 = 0;
  $offset1 = 60;
  
  $rs = pg_execute($pgconn, "_LSELECT", Array($pil,
  		date("Y-m-d H:i", $ts+$offset0),
  		date("Y-m-d H:i", $ts+$offset1)));
  
  if (pg_numrows($rs) < 1){
  	echo "<div class=\"alert alert-warning\">Sorry, could not find product.</div>";
  }
  else{
  	$row = pg_fetch_assoc($rs, 0);
  	$d = preg_replace("/\r\r\n/", "\n", $row["data"]);
  	echo "<pre>". htmlentities($d) ."</pre>\n";
  }
}
?>
