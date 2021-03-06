<?php
require_once("get_pwm.php");

$val[3] = 0;
$val[102] = 10;

echo serialize($val);
echo "<br/>";
$test = 2.5;
echo $test.": ".get_pwmval($val, $test);


?>
