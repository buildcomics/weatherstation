<?php
function get_pollen() {
    $url = $GLOBALS["pollen_url"];
    $hv[0] = "pollen hayfever_good_md";
    $hv[3] = "pollen hayfever_fine_md";
    $hv[7] = "pollen hayfever_bad_md";
    $hv[10] = "pollen hayfever_worst_md";
    
    $doc = new DOMDocument();
    $doc->loadHTML(file_get_contents($url));
    $xpath = new DOMXpath($doc);
    $nlist = $xpath->query("//a[contains(@class,'pollen')]");
    foreach($nlist as $ind => $hyperlink) {
        $hayfever = $hyperlink->getAttribute('class')."\n";
    }
    $hayfever = $nlist[0]->getAttribute('class');
    $key = array_search($hayfever, $hv);
    if ($key === FALSE) {
        return false; //couldn't get hayfever;
    }
    else {
        return $key;
    }
}
?>
