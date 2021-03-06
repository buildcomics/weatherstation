<?php
require_once("get_pwm.php");

$val[26] = -30;
$val[30] = -15;
$val[33] = 0;
$val[39] = 15;
$val[42] = 30;
$val[49] = 70;
$val[52] = 240;
$val[60] = 480;
$val[65] = 1500;

echo serialize($val);
echo "<br/>";
$test = 23;
echo $test.": ".get_pwmval($val, $test);


?>
