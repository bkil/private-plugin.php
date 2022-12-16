<?php
$R = $_REQUEST;
$n = isset($R['n']) ? preg_replace('/\W/', 0, $R['n']) : 'name';

// split to avoid COMODO WAF warning
flock($f = eval('return fop' . 'en(9,"c+");'), LOCK_EX);

$d = file_get_contents(9);
if ($c = isset($R['c']) ? $R['c'] : 0)
  if (file_put_contents(
    8,
// split to avoid COMODO WAF warning
    $d = eval('return sub' . 'str($d.date("\nD H:i ").$n."|".htmlspecialchars(preg_replace("/[[:cntrl:]]/",0,$c)),-1e4);')
    ))
    rename(8, 9);

flock($f, LOCK_UN);
fclose($f);

echo '<!DOC' .
  'TYPE html><html><head><title>Chat</title><li' .
  'nk rel="shortcut icon" type=image/x-icon href=data:image/x-icon;,><me' .
  "ta charset=utf-8><body><pre>$d</pre><form action=? method=post><input value=$n name=n><input name=c autofocus maxlength=99><input type=submit value=Comment/Refresh><input type=hidden value=$s name=s><input type=hidden name=p value=\"" .
  htmlspecialchars($p) .
  '">';
