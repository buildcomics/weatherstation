<?php
require_once("get_pwm.php");

$val[45] = 0;
$val[53] = 10;
$val[59] = 15;
$val[68] = 20;
$val[76] = 25;
$val[85] = 30;
$val[93] = 35;
$val[102] = 40;
$val[110] = 45;
$val[118] = 50;

echo serialize($val);
echo "<br/>";
$test = 23;
echo $test.": ".get_pwmval($val, $test);


?>
