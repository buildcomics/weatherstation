<?php
require_once("get_pwm.php");

$val[0] = -47;
$val[10] = -24;
$val[20] = -15;
$val[30] = -7;
$val[35] = 0;
$val[41] = 5;
$val[47] = 10;
$val[60] = 16;
$val[70] = 23;
$val[80] = 30;
$val[90] = 44;
$val[95] = 50;

echo serialize($val);
echo "<br/>";
$test = 2.5;
echo $test.": ".get_pwmval($val, $test);


?>
