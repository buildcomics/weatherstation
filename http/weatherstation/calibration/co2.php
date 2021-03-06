<?php
require_once("get_pwm.php");

$val[14] = 0;
$val[25] = 1000;
$val[30] = 1500;
$val[35] = 2000;
$val[39] = 2500;
$val[45] = 3000;
$val[50] = 3500;
$val[55] = 4000;
$val[63] = 5000;

echo serialize($val);
echo "<br/>";
$test = 3500;
echo $test.": ".get_pwmval($val, $test);


?>
