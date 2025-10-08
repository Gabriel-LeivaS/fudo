<?php
function hexstr2bin($hex) {
    $s = '';
    for ($i=0; $i<strlen($hex); $i+=2) $s.=chr(hexdec(substr($hex,$i,2)));
    return $s;
}

function QRspec_versionPatternNum($version) {
    return null;
}
