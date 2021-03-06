<?php
require_once("get_pwm.php");

$val[83] = 0;
$val[84] = 10;
$val[86] = 20;
$val[95] = 30;
$val[97] = 40;
$val[100] = 50;
$val[115] = 60;
$val[125] = 70;
$val[145] = 80;
$val[155] = 90;
$val[165] = 100;

echo serialize($val);
echo "<br/>";
$test = 3500;
echo $test.": ".get_pwmval($val, $test);


?>
