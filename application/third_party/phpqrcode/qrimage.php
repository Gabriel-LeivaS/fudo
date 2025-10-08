<?php
function imagepngfromstring($im, $outfile) {
    // $im is raw PNG data string
    file_put_contents($outfile, $im);
}
