<?php
//var_dump($_POST);
echo "pm10s: ".$_POST["pm10s"]."\n";
require_once("../config.php");
require_once("../Meters.php");
$id = $_POST["sensorid"];
if ( $id != 1 && $id = 2) {
    exit ("wrong id given, not 1 or 2!");
}
$pmtypes = array("pm10s", "pm25s", "pm100s", "pm10e", "pm25e", "pm100e");
foreach ($pmtypes as $type) {
    echo "getting ".$type;
    $pmVal=$_POST[$type];
    if (is_numeric($pmVal)) {
        $meters[$type."_".$id] = new Meter($type."_".$id);
        $meters[$type."_".$id]->insert(time(), $pmVal);
        file_put_contents("/srv/http/weatherstation/dust/test.txt", date("c").": ".$pmVal."\n", FILE_APPEND);
    }
    else {
        echo $type." could not be parsed";
    }
}
?>
