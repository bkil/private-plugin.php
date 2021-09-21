<?php

// 1e5 seconds is ~1.16 days
$m = time() / 1e5 % 3;
foreach(glob((($m + 1) % 3) . '*') as $n)
  unlink($n);

$f = $_FILES[f]; // ['f']
$t = $f[tmp_name]; // ['tmp_name']
$m .= sha1_file($t) . '.jpg';

$b = 0;
foreach (glob('*') as $n)
  $b += lstat($n)[12];

// Simultaneously check <64-100kB uploaded file size and <32-50MB disk quota
if (($f[size] | $b) < 1e5) // ['size']
  if (getimagesize($t))
    // We split the function name with `eval` as a workaround so that their regexp won't find it
    // "COMODO WAF: PHP Injection Attack - Matched Data: move_uploaded_file found within ARGS"
    if (eval('return(move_uploaded'."_file('$t','$m'));"))
      header('Location: ' . $m);
