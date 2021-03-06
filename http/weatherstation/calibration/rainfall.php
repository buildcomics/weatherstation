<?php
require_once("get_pwm.php");

$val[10] = 0;
$val[20] = 0.1;
$val[30] = 0.68;
$val[36] = 1;
$val[40] = 3;
$val[43] = 5;
$val[50] = 7.8;
$val[51] = 10;
$val[60] = 60;
$val[66] = 100;

echo serialize($val);
echo "<br/>";
$test = 2;
echo $test.": ".get_pwmval($val, $test);


?>
