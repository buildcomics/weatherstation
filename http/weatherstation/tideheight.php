<?php
function get_tideheight() {
$url = $GLOBALS["water_url"];;
//$url = "https://waterinfo.rws.nl/api/Download/CSV?expertParameter=Waterhoogte___20Oppervlaktewater___20t.o.v.___20Normaal___20Amsterdams___20Peil___20in___20cm&locationSlug=1088&timehorizon=-48,48";
 $csv = file_get_contents($url);
if ($csv === FALSE) {
    echo "couldn't get water info from url: ".$url."\n";
    return false;
}
$lines = explode("\n", $csv);
 $data = array();
$height = false; 
 $compare_epoch = time()-60*60;
 $compare_day = date("j-n-Y",$compare_epoch);
 $compare_minutes = round(date("i",$compare_epoch), -1) %60;
 $compare_time = date("H",$compare_epoch).":".str_pad($compare_minutes,2, "0").":00";
 foreach ($lines as $line) {
   $data = explode(";", $line);
   if (count($data) > 3) {
 	$day = $data[0];
        $time = $data[1];
 	if ($day == $compare_day AND $time == $compare_time) {
 		$height = $data[4];
		break;
 	}
   }
 }
if ($height === false) {
    echo "couldn't get height";
    return false;
}
 
return array($height/100, $compare_epoch);
}
?>
