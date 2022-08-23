<?php
$R = $_REQUEST;

flock($f = fopen(9, 'c+'), LOCK_EX);

$d = file_get_contents(9);
if (isset($R['c']))
  if ($R['c']) {
    if (file_put_contents(
      8,
      $d = substr(
        $d .
        date("\nD H:i ") .
        htmlspecialchars(
          preg_replace('/[[:cntrl:]]/', 0, $R['c'])),
        -1e4))
      )
      rename(8, 9);
  }

flock($f, LOCK_UN);
fclose($f);

echo '<!DOC' .
  'TYPE html><html><head><title>Chat</title><li' .
  'nk rel="shortcut icon" type=image/x-icon href=data:image/x-icon;,><me' .
  "ta charset=utf-8><body><pre>$d</pre><form action=? method=post><input name=c autofocus maxlength=99><input type=submit value=Comment/Refresh><input type=hidden value=$s name=s><input type=hidden name=p value=\"" .
  htmlspecialchars($p) .
  '">';
