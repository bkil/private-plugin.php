<?php
// 1e5 seconds is ~1.16 days
$m = time() / 1e5 % 9;
// cleanup old polls after ~10 days
foreach(glob((($m + 1) % 9) . '*') as $n)
  unlink($n);

// break some HTML tags to avoid Comodo WAF firewall warnings
echo '<!DOC' . 'TYPE html><html><head><me' .
  'ta charset=utf-8><title>Poll</title><li' .
  'nk rel="shortcut icon" type=image/x-icon href=data:image/x-icon;,><s' .
  'tyle>input,label,textarea{display:block}</style><body><form action=? method=post>';

if (isset($_REQUEST['d']) && isset($_REQUEST['o'])) {
  // new poll: d o
  $d = $_REQUEST['d'];
  $o = $_REQUEST['o'];

  // get votes on poll: d o e
  $e = isset($_REQUEST['e']) && preg_match('/^[0-8]$/', $_REQUEST['e']) ? $_REQUEST['e'] : $m;

  echo '<textarea readonly name=d>' . htmlspecialchars($d) . '</textarea><table><tr><th>Name';
  $O = '';
  foreach ($o as $i => $q) {
    // stop when seeing first empty choice
    if (!$q) {
      $o = array_slice($o, 0, $i);
      break;
    }
    $O .= '&o[]=' . urlencode($q);
    echo '<th><input readonly name=o[] value="' . htmlspecialchars($q) . '">';
  }

  $D = urlencode($d);
  $f = $e . sha1($D . $O) . '.txt';

  // honor disk quota
  $b = 0;
  foreach (glob('*') as $g)
    $b += lstat($g)[12];
  $w = $b < 1e3;

  $V = json_decode(@file_get_contents($f));
  if ($w)
    if (isset($_REQUEST['n'])) {
      // place vote on a poll: d o e n v
      $n = $_REQUEST['n'];
      $v = isset($_REQUEST['v']) ? $_REQUEST['v'] : [];

      array_unshift($v, $n);
      $V[] = $v;
      $t = tempnam('', '');
      if (file_put_contents($t, json_encode($V)))
        rename($t, $f);
    }

  $C = array_fill(0, count($o)+1, 0);
  if ($V)
    foreach ($V as $v) {
      echo '<tr><td>' . htmlspecialchars($v[0]);
      $C[0]++;
      $j = 1;
      foreach ($o as $i => $q) {
        $c = '';
        if (isset($v[$j]))
          // converting them to numbers upon input would have been better, but longer
          if ($v[$j] == $i) {
            $C[$i + 1]++;
            $c = ' checked';
            $j++;
          }
        echo '<td><input disabled type=checkbox' . $c . '>';
      }
    }

  echo '<tr>';
  foreach ($C as $c)
    echo '<td>' . $c;

  if ($w) {
    echo '<tr><td><input required name=n>';
    foreach($o as $i=>$q)
      echo '<td><input name=v[] value=' . $i . ' type=checkbox>';
    echo '</table><input type=submit value=Vote>';
  }
  $u = urlencode($p);
  $S = urlencode($s);
  echo "<label><a href=\"?p=$u&s=$S&d=$D$O&e=$e\">Share link</a></label><label><a href=\"?p=$u&s=$S\">New poll</a></label>";
} else {
  // HTML index to create new poll
  echo "<input type=submit value=CreatePoll><label>Description<textarea required name=d></textarea><label>Choices</label><input required name=o[]>";
  for ($i=7; $i; $i--)
    echo "<input name=o[]>";
}

$P = htmlspecialchars($p);
echo "<input type=hidden name=e value=$e><input type=hidden name=p value=\"$P\"><input type=hidden value=$s name=s>";
