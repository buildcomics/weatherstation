<?php
require_once("config.php");
require_once ('Netatmo/autoload.php');
require_once("Meters.php");
require_once("write_pwmvalues.php");
require_once("tideheight.php");
require_once("MoonPhase.php");
require_once("sunsetrise.php");
require_once("kpindex.php");
require_once("pollen.php");

$meters["temp_in_meter"] = new Meter("temp_in");
$meters["temp_out_meter"] = new Meter("temp_out");
$meters["hum_in_meter"] = new Meter("humidity_in");
$meters["hum_out_meter"] = new Meter("humidity_out");
$meters["co2_meter"] = new Meter("co2_in");
$meters["wind_speed_meter"] = new Meter("wind_speed");
$meters["wind_dir_meter"] = new Meter("wind_dir");
$meters["gust_speed_meter"] = new Meter("gust_speed");
$meters["gust_dir_meter"] = new Meter("gust_dir");
$meters["rain_meter"] = new Meter("rainfall");
$meters["pressure"] = new Meter("pressure");
$meters["sound_level"] = new Meter("sound_level");
$meters["tide_height"] = new Meter("tide_height");
$meters["moon_phase"] = new Meter("moon_phase");
$meters["sunsetrise"] = new Meter("sunsetrise");
$meters["uv_strength"] = new Meter("uv_strength");
$meters["aurora_borealis"] = new Meter("aurora_borealis");
$meters["pollencount"] = new Meter("pollencount");
$meters["cloud_cover"] = new Meter("cloud_cover");

//App client configuration
$scope = Netatmo\Common\NAScopes::SCOPE_READ_STATION;
$config = array("client_id" => $GLOBALS["client_id"],
                "client_secret" => $GLOBALS["client_secret"],
                "username" => $GLOBALS["test_username"],
                "password" => $GLOBALS["test_password"]);

$client = new Netatmo\Clients\NAWSApiClient($config);

try { //Authentication with Netatmo server (OAuth2)
    $tokens = $client->getAccessToken();
}
catch(Netatmo\Exceptions\NAClientException $ex) {
    handleError("A damn error happened while trying to retrieve your tokens: " .$ex->getMessage()."\n", TRUE);
}
$minutes = (date("i") % 10);
$seconds = $minutes*60 + date("s")+2;
$delay = 10*60;
echo date(DATE_RFC2822).PHP_EOL;
$tenminutes = new EvPeriodic($seconds, $delay, NULL, function ($tenminutes, $revents) use ($client,$meters) { //run every 10 minutes
    echo "tenminutes: ".date(DATE_RFC2822).PHP_EOL;
    try { //Retrieve user's Weather Stations Information
        $data = $client->getData(NULL, TRUE);
    }
    catch(Netatmo\Exceptions\NAClientException $ex) {
        echo "Couldn't connect to netatmo...";        
        handleError("An error occured while retrieving data: ". $ex->getMessage()."\n", TRUE);
    }
    
    if(empty($data['devices'])) {
        echo 'No devices affiliated to user';
    }
    else
    {
        if (!empty($data["devices"][0]["dashboard_data"]["time_utc"])) {
            $temp_in = $data["devices"][0]["dashboard_data"]["Temperature"];
            $temp_in_time = $data["devices"][0]["dashboard_data"]["time_utc"];
            
            $co2 = $data["devices"][0]["dashboard_data"]["CO2"];
            $co2_time = $data["devices"][0]["dashboard_data"]["time_utc"];
            
            $hum_in = $data["devices"][0]["dashboard_data"]["Humidity"];
            $hum_in_time = $data["devices"][0]["dashboard_data"]["time_utc"];
            
            $pressure = $data["devices"][0]["dashboard_data"]["Pressure"];
            $pressure_time = $data["devices"][0]["dashboard_data"]["time_utc"];
           
            if (!empty($data["devices"][0]["dashboard_data"]["Noise"])) {
                $sound_level = $data["devices"][0]["dashboard_data"]["Noise"];
                $sound_level_time = $data["devices"][0]["dashboard_data"]["time_utc"];
                $meters["sound_level"]->insert($sound_level_time, $sound_level);
            }
            else {
                echo "No noise data?";
            } 
            $meters["temp_in_meter"]->insert($temp_in_time, $temp_in);
            $meters["hum_in_meter"]->insert($hum_in_time, $hum_in);
            $meters["co2_meter"]->insert($co2_time, $co2);
            $meters["pressure"]->insert($pressure_time, $pressure);
        }
        else {
            echo "no existing time for netatmo dashboard?";
        } 
        
        if (!empty($data["devices"][0]["modules"][0]["dashboard_data"]["time_utc"])) {
            $temp_out = $data["devices"][0]["modules"][0]["dashboard_data"]["Temperature"];
            $temp_out_time = $data["devices"][0]["modules"][0]["dashboard_data"]["time_utc"];
            
            $hum_out = $data["devices"][0]["modules"][0]["dashboard_data"]["Humidity"];
            $hum_out_time = $data["devices"][0]["modules"][0]["dashboard_data"]["time_utc"];
            
            $meters["hum_out_meter"]->insert($hum_out_time,$hum_out);
            $meters["temp_out_meter"]->insert($temp_out_time, $temp_out);
            
        }
        else {
            echo "no existing time for netatmo module 0 (outside module)?";
        } 

        if (!empty($data["devices"][0]["modules"][1]["dashboard_data"]["time_utc"])) {
            $wind_speed = $data["devices"][0]["modules"][1]["dashboard_data"]["WindStrength"];
            $wind_speed_time = $data["devices"][0]["modules"][1]["dashboard_data"]["time_utc"];
            
            $wind_dir = $data["devices"][0]["modules"][1]["dashboard_data"]["WindAngle"];
            $wind_dir_time = $data["devices"][0]["modules"][1]["dashboard_data"]["time_utc"];
            
            $gust_speed = $data["devices"][0]["modules"][1]["dashboard_data"]["GustStrength"];
            $gust_speed_time = $data["devices"][0]["modules"][1]["dashboard_data"]["time_utc"];
            
            $gust_dir = $data["devices"][0]["modules"][1]["dashboard_data"]["GustAngle"];
            $gust_dir_time = $data["devices"][0]["modules"][1]["dashboard_data"]["time_utc"];
            
            $meters["wind_speed_meter"]->insert($wind_speed_time, $wind_speed);
            $meters["wind_dir_meter"]->insert($wind_dir_time, $wind_dir);
            $meters["gust_speed_meter"]->insert($gust_speed_time, $gust_speed);
            $meters["gust_dir_meter"]->insert($gust_dir_time, $gust_dir);
        }
        else {
            echo "no existing time for netatmo module 1 (= wind)?";
        } 

        if (!empty($data["devices"][0]["modules"][2]["dashboard_data"]["time_utc"])) {
             
            $rain = $data["devices"][0]["modules"][2]["dashboard_data"]["Rain"];
            $rain_time = $data["devices"][0]["modules"][2]["dashboard_data"]["time_utc"];
            
            $meters["rain_meter"]->insert($rain_time, $rain);
        }
        else {
            echo "no existing time for netatmo module 2 (= rain)?";
        } 
        
    }
    
    //get tide height:
    $tideheight = get_tideheight();
    if ($tideheight === FALSE) {
        echo "couldn't get tide height...";
    }
    else {
        $meters["tide_height"]->insert($tideheight[1], $tideheight[0]);
    }
    
    //get time to sunset/sunrise:
    $sunsetrise = get_sunsetrise();
    $meters["sunsetrise"]->set($sunsetrise, time());

    //get UV index:
    $w_data = file_get_contents($GLOBALS["w_url"]);
    if ($w_data !== FALSE) {
        $w_json = json_decode($w_data,true);
        if (array_key_exists("current_observation", $w_json) === TRUE) { 
            $uv = $w_json["current_observation"]["UV"];
            $uv_time = $w_json["current_observation"]["observation_epoch"];
            $meters["uv_strength"]->insert($uv_time, $uv);
        }
        else {
            echo "Coulldn't get current observation for UV data";
        }
    }
    else {
        echo "Could not retrieve UV data";
    }

    //write to beaglebone:
    write_pwmvalues();
    write_servovalues();
});

$minutes = (date("i") % 30);
$seconds = $minutes*60 + date("s")+2;
$delay = 30*60;
$thirtyminutes = new EvPeriodic($seconds, $delay, NULL, function ($thirtyminutes, $revents) use ($client,$meters) { //run every 10 minutes
    echo "thirtyminutes: ".date(DATE_RFC2822).PHP_EOL;

    //get aurora borealis:
    $kp_ind = get_kp();
    if ($kp_ind === FALSE) {
        echo "couldn't get kp index...";
    }
    else {
        $meters["aurora_borealis"]->insert(time(), $kp_ind);
    }
});

$minutes = date("i");
$seconds = $minutes*60 + date("s")+2;
$delay = 60*60;
$hour = new EvPeriodic($seconds, $delay, NULL, function ($hour, $revents) use ($client,$meters) { //run every 10 minutes
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
});
Ev::run();
?>
