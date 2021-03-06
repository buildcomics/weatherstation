<?php
require_once("get_pwm.php");

$val[38] = 0;
$val[50] = 1.3;
$val[60] = 2.6;
$val[70] = 4.3;
$val[80] = 5.65;
$val[90] = 6.8;
$val[100] = 8.1;
$val[110] = 9.75;
$val[120] = 10.6;
$val[130] = 11.6;
$val[136] = 12;

echo serialize($val);
echo "<br/>";
$test = 5;
echo $test.": ".get_pwmval($val, $test);


?>
