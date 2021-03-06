<?php
require_once("config.php");
require_once("Meters.php");
require_once("kpindex.php");

$meters["aurora_borealis"] = new Meter("aurora_borealis");

echo "thirtyminutes: ".date(DATE_RFC2822).PHP_EOL;
//get aurora borealis:
$kp_ind = get_kp();
if ($kp_ind === FALSE) {
    echo "couldn't get kp index...";
}
else {
    $meters["aurora_borealis"]->insert(time(), $kp_ind);
}
?>
