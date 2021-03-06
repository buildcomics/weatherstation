<?php
echo "Strikes: ".$_GET["strikes"]."\n";
require_once("../config.php");
require_once("../Meters.php");
require_once("../write_pwmvalues.php");
if ( is_numeric($_GET["strikes"]) ) {
    $meters["lightning"] = new Meter("lightning_strikes");
    $meters["lightning"]->insert(time(), $_GET["strikes"]);
    write_pwmvalues();
}
else {
    echo "could not parse strikes";
}
?>
