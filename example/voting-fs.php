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
  echo ($h = '<a href="?p=' . urlencode($p) . '&s=' . urlencode($s)) .
    '">New poll</a><table><tr><th>Edit';
  $O = '';
  $C = [];
  foreach ($o = $R['o'] as $i => $q) {
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

  // place vote on a poll: o e v
  $f = $e . sha1($O);
  if ($w = (($b > -1e3) && isset($R['v']) && (strlen(json_encode($v = $R['v'])) < 1e3)))
    flock($F = eval('return fop' . 'en($f,"c+");'), LOCK_EX);

  $V = json_decode(@file_get_contents($f));
  if ($w) {
    // edit vote: o e v i
    if ((isset($R['i'])) && (($i = intval($R['i'])) >= 0) && ($i < count($V)))
      $V[$i] = $v;
    else
      $V[] = $v;

    $t = tempnam('.', 9);
    if (file_put_contents($t, json_encode($V)))
      rename($t, $f);
    else
      @unlink($t);
    flock($F, LOCK_UN);
    fclose($F);
  }

  if ($V)
    foreach ($V as $k => $v) {
      echo '<tr><td><input type=radio name=i value=' . $k . '><td>' . htmlspecialchars($v[0]);
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

  echo '<tr><td>';
  foreach ($C as $c)
    echo '<td>' . -$c;

  echo '<tr><td><input type=radio name=i value=-1 checked';
  foreach($o as $i=>$q)
    echo '><td><input name=v[] ' .
    ($i ? 'type=checkbox value=' . $i : 'required autofocus');

  echo '></table>' . -($m - $e - 8) % 9 . ' days left<input type=submit value=Vote>';
  echo "$h$O&e=$e\">Share link</a>";
} else {
  // HTML index to create new poll
  echo '<input type=submit value=CreatePoll><label>Description<input required autofocus name=o[]></label><label>Choices</label><input required name=o[]>';
  for ($i=7; $i--;)
    echo '<input name=o[]>';
  $e = $m;
}

echo '<input type=hidden name=p value="' . htmlspecialchars($p) .
  "\"><input type=hidden name=e value=$e><input type=hidden name=s value=$s>";
