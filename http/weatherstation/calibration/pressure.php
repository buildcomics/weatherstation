<?php
require_once("get_pwm.php");

$val[22] = 930;
$val[30] = 950;
$val[44] = 975;
$val[59] = 1000;
$val[75] = 1025;
$val[93] = 1050;

echo serialize($val);
echo "<br/>";
$test = 1012.5;
echo $test.": ".get_pwmval($val, $test);


?>
