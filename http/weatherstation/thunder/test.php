<?php
echo "test: ".$_POST["test"]."\n";
file_put_contents("/srv/http/weatherstation/thunder/test.txt", date("c").": ".$_POST["test"]."\n", FILE_APPEND);
?>
