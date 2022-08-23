<?php
$R = $_REQUEST;

flock($f = fopen('w', 'c+'), LOCK_EX);

$d = stream_get_contents($f);
if (isset($R['c']))
  if ($R['c']) {
    ftruncate($f, 0);
    rewind($f);
    fwrite($f,
      $d = substr(
        $d .
        date("\nD H:i ") .
        htmlspecialchars(
          preg_replace('/[[:cntrl:]]/', '', $R['c'])),
        -1e4));
    fflush($f);
  }

flock($f, LOCK_UN);
fclose($f);

echo '<!DOC' .
  'TYPE html><html><head><title>Chat</title><li' .
  'nk rel="shortcut icon" type=image/x-icon href=data:image/x-icon;,><me' .
  "ta charset=utf-8><body><pre>$d</pre><form action=? method=post><input name=c autofocus maxlength=99><input type=submit value=Comment/Refresh><input type=hidden value=$s name=s><input type=hidden name=p value=\"" .
  htmlspecialchars($p) .
  '">';
