<?php

include_once '../include/minify.php';

$s = file_get_contents('../../example/publish-pic-be.php');
$s = urlencode_unsafe(minify_php($s));

echo $s . PHP_EOL;
