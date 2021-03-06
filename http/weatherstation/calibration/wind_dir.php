<?php
require_once("get_pwm.php");

$val[0] = 10;
$val[34] = 180;
$val[63] = 360;

echo serialize($val);
echo "<br/>";
$test = 270;
echo $test.": ".get_pwmval($val, $test);


?>
