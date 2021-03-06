<?php
require_once("get_pwm.php");

$val[5] = 0;
$val[255] = 7.95;

echo serialize($val);
echo "<br/>";
$test = 6;
echo $test.": ".get_pwmval($val, $test);


?>
