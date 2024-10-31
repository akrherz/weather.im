<?php
date_default_timezone_set('America/Chicago');

$pid = isset($_GET['pid']) ? substr($_GET['pid'], 0, 36) : "";

$content = file_get_contents("http://mesonet.agron.iastate.edu/api/1/nwstext/{$pid}");

if ($content === FALSE){
      echo "<div class=\"alert alert-warning\">Sorry, could not find product.</div>";
} else{
      echo "<pre>". htmlentities($content) ."</pre>\n";
}
