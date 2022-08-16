<?php
//1551
// 1e5 seconds is ~1.16 days
$m = time() / 1e5 % 9;
// cleanup old polls after ~10 days
foreach(glob((($m + 1) % 9) . '*') as $n)
  unlink($n);

// break some HTML tags to avoid Comodo WAF firewall warnings
echo '<!DOC' . 'TYPE html><html><form>';
$R=$_REQUEST;
if (preg_match('/^[0-8]$/', $e = $R[e])) {
  // new poll: o e
  $P = urlencode($p);
  $S = urlencode($s);
  echo "<a href=\"?p=$P&s=$S\">New poll</a><table><tr>";
  foreach ($o = $R[o] as $q) {
    $O .= '&o[]=' . urlencode($q);
    echo '<th><input name=o[] value="' . htmlspecialchars($q) . '">';
  }

  // honor disk quota
  $b = 0;
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

  echo "></table><a href=\"?p=$P&s=$S$O&e=$e\">Share</a>";
} else {
  // HTML index to create new poll
  $e = $m;
  echo '<label>Choices</label>';
  for ($i=5; $i--;)
    echo '<input name=o[]>';
  echo '<label>Description<input name=o[]></label>';
}

echo '<input type=submit><input name=p value="' . htmlspecialchars($p) .
  '"><input name=e value=' . $e .
  '><input name=s value=' . $s . '>';
