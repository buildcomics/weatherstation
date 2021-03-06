<?php
require_once("config.php");
require_once("Meters.php");
require_once("MoonPhase.php");
require_once("pollen.php");

$meters["pollencount"] = new Meter("pollencount");
$meters["moon_phase"] = new Meter("moon_phase");
$meters["cloud_cover"] = new Meter("cloud_cover");
$meters["fn_outside"] = new Meter("fn_outside");
$meters["pm25_outside"] = new Meter("pm25_outside");

echo "hour: ".date(DATE_RFC2822).PHP_EOL;

//get pollencount:
$pollencount = get_pollen();
if ($pollencount === FALSE) {
    echo "couldn't get kp index...";
}
else {
    $meters["pollencount"]->insert(time(), $pollencount);
}

//get cloud cover:
$cloud_data = file_get_contents($GLOBALS["openweathermap_url"]);
$cloud_json = json_decode($cloud_data,true);
$clouds = 8*($cloud_json["clouds"]["all"]/100);
$clouds_time = $cloud_json["dt"];
$meters["cloud_cover"]->insert($clouds_time, $clouds);

//get moon phase
$moon = new Solaris\MoonPhase();
$illumination = round($moon->illumination()*100);
$meters["moon_phase"]->insert(time(), $illumination);

//get fine dust and pm25 outside
$start = date("Y-m-d\TH:i:s\Z",time()-60*60*4); //get dat from last hour
$end = date("Y-m-d\TH:i:s\Z", time()+60*60*4);
$url = $GLOBALS["luchtmeenet_url"]."&start=".$start."&status=&end=".$end;
$fijnmeet_data = file_get_contents($url);
$fijnmeet_json = json_decode($fijnmeet_data,true);
foreach ($fijnmeet_json["data"] as $measurement) {
  $val = $measurement["value"];
  $time = $measurement["timestamp_measured"];
  $timestamp = strtotime($time);
  if ($measurement["formula"] == "PM25") {
    $meters["pm25_outside"]->insert($timestamp, $val);
  }
  elseif ($measurement["formula"] == "FN") {
    $meters["fn_outside"]->insert($timestamp, $val);
  }
}

?>
