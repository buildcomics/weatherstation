<?php
require_once("get_pwm.php");

$val[18] = 0;
$val[21] = 24;
$val[25] = 50;
$val[28] = 75;
$val[30] = 91;
$val[31] = 100;

echo serialize($val);
echo "<br/>";
$test = 60;
echo $test.": ".get_pwmval($val, $test);


?>
