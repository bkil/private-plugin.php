<?php
// 1e5 seconds is ~1.16 days
// clean up old data after ~4 days
foreach (glob((($m = time() / 1e5 & 7) ^ 4) . '*') as $g)
  unlink($g);

$R = $_REQUEST;
if (isset($R['u'])) {
  if (preg_match('/^\w+$/', $u = $R['u']))
    header('Location: ' . @file_get_contents($u));
  else if (preg_match('/^https?:[ -~]{1,9000}$/', $u)) {
    $b = 0;
    foreach (glob('*') as $g)
      $b += lstat($g)[12];

    if ($b < 1e4)
      file_put_contents($m .= sha1($u), $u);

    echo $m;
  }
}
