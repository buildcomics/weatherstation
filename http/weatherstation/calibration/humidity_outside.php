<?php
require_once("get_pwm.php");

$val[19] = 0;
$val[21] = 10;
$val[26] = 20;
$val[32] = 30;
$val[37] = 40;
$val[42] = 50;
$val[47] = 60;
$val[53] = 70;
$val[57] = 80;
$val[63] = 90;
$val[67] = 100;

echo serialize($val);
echo "<br/>";
$test = 25;
echo $test.": ".get_pwmval($val, $test);


?>
