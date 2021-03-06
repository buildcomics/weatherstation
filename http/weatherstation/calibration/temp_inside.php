<?php
require_once("get_pwm.php");

$val[10] = -10;
$val[26] = 0;
$val[35] = 5;
$val[42] = 10;
$val[51] = 15;
$val[58] = 20;
$val[69] = 25;
$val[74] = 29.8;
$val[80] = 35;
$val[88] = 40;

echo serialize($val);
echo "<br/>";
$test = 22.4;
echo $test.": ".get_pwmval($val, $test);


?>
