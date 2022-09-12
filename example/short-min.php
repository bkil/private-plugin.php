<?php
// 1e5 seconds is ~1.16 days
// clean up old data after ~4 days
foreach (glob((($m = time() / 1e5 & 7) ^ 4) . '*') as $g)
  unlink($g);

if (preg_match('/^\w+$/', $u = $_REQUEST[u]))
  header('Location: ' . file_get_contents($u));

// honor disk quota
foreach (glob('*') as $g)
  $b += lstat($g)[12];

if ($b < 1e4)
  if (preg_match('/^https?:[ -~]+$/', $u))
    file_put_contents($m . sha1($u), $u);
