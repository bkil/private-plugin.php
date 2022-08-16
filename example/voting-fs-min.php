<?php
$R=$_REQUEST;
// 1e5 seconds is ~1.16 days
$e = time() / 1e5 % 9;
// clean up old polls after ~10 days
foreach(glob((($e + 1) % 9) . '*') as $n)
  unlink($n);

$h = '><a href="?p=' . urlencode($p) . '&s=' . urlencode($s);
// break some HTML tags to avoid Comodo WAF firewall warnings
echo '<!DOC' . 'TYPE html><html><form' . $h . '">New poll</a><table><tr><th colspan=5>Choices<th>Description<tr>';

for ($i=6; $i--;) {
  $q = $R[o][5-$i];
  $O .= '&o[]=' . urlencode($q);
  echo '<th><input name=o[] value="' . htmlspecialchars($q) . '">';
}

if (preg_match('/^[0-8]$/', $R[e])) {
  // new poll: o e
  $e = $R[e];

  // honor disk quota
  foreach (glob('*') as $g)
    $b -= lstat($g)[12];

  $V = json_decode(file_get_contents($f = $e . sha1($O)));
  if ($b > -1e3)
    if ($R[v][0]) {
      // place vote on a poll: o e v
      $V[] = $R[v];
      file_put_contents($f, json_encode($V));
    }

  foreach ($V as $v) {
    echo '<tr';
    $C[0] -= 1;
    $j = 0;
    for ($i=5; $i; $i--) {
      $c = 0;
      // converting them to numbers upon input would have been better, but longer
      if ($v[$j] == $i) {
        $C[$i] -= 1;
        $c = checked;
        $j++;
      }
      echo '><td><input type=checkbox ' . $c;
    }
    echo '><td>' . htmlspecialchars($v[$j]);
  }

  echo '<tr>';
  for ($i=6; $i--;)
    echo '<td>' . -$C[$i];

  echo '<tr';
  for ($i=6; $i--;)
    echo '><td><input name=v[] ' .
    ($i ? 'type=checkbox value=' . $i : 0);

  echo "$h$O&e=$e\">Share</a>";
}

echo '</table><input type=submit><input name=p value="' . htmlspecialchars($p) .
  "\"><input name=e value=$e><input name=s value=$s>";
