<?php
$R = $_REQUEST;

$d = @file_get_contents('w');
if (isset($R['c']))
  if ($R['c'])
    file_put_contents('w',
      $d = substr(
        $d .
        date("\nD H:i ") .
        htmlspecialchars(
          preg_replace('/[[:cntrl:]]/', '', $R['c'])),
        -1e4));

echo '<!DOC' .
  'TYPE html><html><head><title>Chat</title><li' .
  'nk rel="shortcut icon" type=image/x-icon href=data:image/x-icon;,><me' .
  "ta charset=utf-8><body><pre>$d</pre><form action=? method=post><input name=c autofocus maxlength=99><input type=submit value=Comment/Refresh><input type=hidden name=s value=$s><input type=hidden name=p value=\"" .
  htmlspecialchars($p) .
  '">';
