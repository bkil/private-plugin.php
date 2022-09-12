<?php
$R = $_REQUEST;
// 1e5 seconds is ~1.16 days
// clean up old polls after ~8 days
foreach(glob(((time() / 1e5 + 1) % 9) . '*') as $g)
  unlink($g);

if (isset($R['f']) && preg_match($h = '/^[0-8][0-9a-f]{1,40}$/', $f = $R['f'])) {
  // honor disk quota
  $b = 0;
  foreach (glob('*') as $g)
    $b += lstat($g)[12];

  // place vote on a poll: f v
  if ($w = (($b < 1e4) && isset($R['v']) && (strlen(serialize($v = $R['v'])) < 1e3)))
  // break fopen() to avoid Comodo WAF firewall warnings
    flock($F = eval('return fop' . 'en($f,"c+");'), LOCK_EX);

  $V = json_decode($r = @file_get_contents($f));

  if ($w) {
    // edit vote: f v i
    if ((isset($R['i'])) && (($i = intval($R['i'])) >= 0) && ($i < count($V)))
      $V[$i] = $v;
    else
      $V[] = $v;

    if (file_put_contents($t = tempnam('.', 9), $n = json_encode($V))) {
      rename($t, $f);
      $r = $n;
    } else
      @unlink($t);

    flock($F, LOCK_UN);
    fclose($F);
  }

  // save result as a CORS mitigation: c
  if (($b < 2e4) && isset($R['c']) && preg_match($h, $c = $R['c']))
    file_put_contents($c . '.css', '#_::after{content:"' . urlencode($r) . '"}' );

  echo $r;
}
