<?php
$R = $_REQUEST;
// 1e5 seconds is ~1.16 days
$m = time() / 1e5 % 9;
// cleanup old polls after ~9 days
foreach(glob((($m + 1) % 9) . '*') as $g)
  unlink($g);

// break some HTML tags to avoid Comodo WAF firewall warnings
echo '<!DOC' . 'TYPE html><html><head><me' .
  'ta charset=utf-8><title>Poll</title><li' .
  'nk rel="shortcut icon" type=image/x-icon href=data:image/x-icon;,><s' .
  'tyle>input,label{display:block}</style><body><form action=? method=post>';

if (isset($R['o']) && isset($R['e']) && preg_match('/^[0-8]$/', $e = $R['e'])) {
  // get votes on poll: o e
  $o = $R['o'];

  $h = '<a href="?p=' . urlencode($p) . '&s=' . urlencode($s);
  echo $h . '">New poll</a><table><tr>';
  $O = '';
  $C = [];
  foreach ($o as $i => $q) {
    // stop after seeing first empty choice
    if (!$q) {
      $o = array_slice($o, 0, $i);
      break;
    }
    $C[] = 0;
    $O .= '&o[]=' . urlencode($q);
    echo '<th><input readonly name=o[] value="' . htmlspecialchars($q) . '">';
  }

  // honor disk quota
  $b = 0;
  foreach (glob('*') as $g)
    $b -= lstat($g)[12];

  $V = json_decode(@file_get_contents($f = $e . sha1($O)));
  if ($b > -1e3)
    if (isset($R['v'])) {
      // place vote on a poll: o e v
      $V[] = $R['v'];
      $t = tempnam('', '');
      if (file_put_contents($t, json_encode($V)))
        rename($t, $f);
    }

  if ($V)
    foreach ($V as $v) {
      echo '<tr><td>' . htmlspecialchars($v[0]);
      $C[0]--;
      $j = 1;
      foreach ($o as $i => $q)
        if ($i) {
          $c = 0;
          if (isset($v[$j]))
            // converting them to numbers upon input would have been better, but longer
            if ($v[$j] == $i) {
              $C[$i]--;
              $c = 'checked';
              $j++;
            }
          echo '<td><input disabled type=checkbox ' . $c . '>';
        }
    }

  echo '<tr>';
  foreach ($C as $c)
    echo '<td>' . -$c;

  echo '<tr';
  foreach($o as $i=>$q)
    echo '><td><input name=v[] ' .
    ($i ? 'type=checkbox value=' . $i : 'required');

  echo '></table>' . -($m - $e - 8) % 9 . ' days left<input type=submit value=Vote>';
  echo "$h$O&e=$e\">Share link</a>";
} else {
  // HTML index to create new poll
  echo '<input type=submit value=CreatePoll><label>Description<input required name=o[]></label><label>Choices</label><input required name=o[]>';
  for ($i=7; $i--;)
    echo '<input name=o[]>';
  $e = $m;
}

echo '<input type=hidden name=p value="' . htmlspecialchars($p) .
  "\"><input type=hidden name=e value=$e><input type=hidden name=s value=$s>";
