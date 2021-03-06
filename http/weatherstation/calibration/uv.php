<?php
require_once("get_pwm.php");

$val[0] = 0;
$val[10] = 2.45;
$val[20] = 3.50;
$val[28] = 5;
$val[40] = 7.25;
$val[51] = 8.75;
$val[56] = 10;

echo serialize($val);
echo "<br/>";
$test = 2;
echo $test.": ".get_pwmval($val, $test);


?>
