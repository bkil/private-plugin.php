<?php
include_once '../include/minify-js-parser.php';

if ($argc === 2) {
  $s = file_get_contents($argv[1]);
  $s = lexer($s);
  echo $s . PHP_EOL;
} else {
  error_log("usage: {$argv[0]} [input.js]");
}
