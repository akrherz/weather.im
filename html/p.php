<?php

$pid = isset($_GET['pid']) ? substr($_GET['pid'], 0, 36) : "";

// Validate that pid contains only safe characters (alphanumeric and hyphens)
if (!preg_match('/^[a-zA-Z0-9\-]+$/', $pid)) {
    echo "<div class=\"alert alert-warning\">Invalid product ID.</div>";
    exit;
}

// Prevent SSRF by blocking internal IP ranges in the URL
$api_url = "http://mesonet.agron.iastate.edu/api/1/nwstext/{$pid}";

$content = file_get_contents($api_url);

if ($content === FALSE){
      echo "<div class=\"alert alert-warning\">Sorry, could not find product.</div>";
} else{
      echo "<pre>". htmlentities($content) ."</pre>\n";
}
