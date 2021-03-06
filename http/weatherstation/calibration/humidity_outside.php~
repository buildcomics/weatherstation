<?php
require_once("get_pwm.php");

$val[19] = -10;
$val[26] = -5;
$val[30] = -3;
$val[40] = -0.3;
$val[54] = -0.1;
$val[65] = 0;
$val[77] = 0.1;
$val[89] = 0.2;
$val[105] = 1;
$val[135] = 5;
$val[195] = 10;

echo serialize($val);
echo "<br/>";
$test = 0.05;
echo $test.": ".get_pwmval($val, $test);


?>
