<?php 
require_once "../config/settings.inc.php";
require_once "../include/myview.php";

$t = new MyView();
$t->content = <<<EOF
<h3>Weather.IM</h3>

<p>This website is a testbed of weather dissemination technologies powered
by the Iowa Environmental Mesonet of Iowa State University.  These products
and services are provided without warranty.

<ul>
 <li><a href="/iembot/">IEMBot Monitor</a></li>
 <li><a href="/live/">Live Client</a>  This is an upstream version of the
 <a href="https://nwschat.weather.gov/live/">NWSChat Live</a> client. This 
 client interfaces with the XMPP server hosted at weather.im and open to
 public usage.</li>
</ul>
EOF;
$t->render('single.phtml');
?>