<?php
//var_dump($_POST);
echo "up: ".$_GET["up"]."\n";
require_once("../config.php");
require_once("../Meters.php");
if ( is_numeric($_GET["up"]) ) {
    $meters["up"] = new Meter("internet_speed_up");
    $meters["up"]->insert(time(), $_GET["up"]);
}
else {
    echo "could not parse up speed";
}
if ( is_numeric($_GET["down"]) ) {
    $meters["down"] = new Meter("internet_speed_down");
    $meters["down"]->insert(time(), $_GET["down"]);
}
else {
    echo "could not parse down speed";
}
?>
