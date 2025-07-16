<?php

//
// some of these shamelessly borrowed from the great Merlin Mann
//
// https://gist.github.com/merlinmann/d4c137662eea4b27ed0b0a506c467044
//

$slugfile  = $_SERVER['DOCUMENT_ROOT'] . '/slugs.txt';
$sluglines = file($slugfile);

echo strtolower($sluglines[mt_rand(0,count($sluglines)-1)]);

?>
