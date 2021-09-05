<?php
function subdirs($d) {
  return array_diff(scandir($d, SCANDIR_SORT_NONE), array('.', '..'));
}
function rmdirs($d) {
  if (is_dir($d)) {
    foreach(subdirs($d) as $n)
      rmdirs($d . '/' . $n);
    rmdir($d);
  } else if (file_exists($d)) {
    unlink($d);
  }
}
function dirsize($d) {
  $i = 1;
  $s = lstat($d)[12] * 512;
  if (is_dir($d))
    foreach (subdirs($d) as $n) {
      $c = dirsize($d . '/' . $n);
      $i += $c[0];
      $s += $c[1];
    }
  return array($i, $s);
}
$e = true;
$m = time() / 86400 % 3;
if (isset($_FILES['f'])) {
  $f = $_FILES['f'];
  $t = $f['tmp_name'];
  $s = $f['size'];
  $c = dirsize('.');
  if (($f['error'] === UPLOAD_ERR_OK) && ($s < 1e5) && ($s + $c[1] < 1e6) && ($c[0] < 1e3) && (is_uploaded_file($t)) && (@mime_content_type($t) == 'image/jpeg')) {
    $o = strval($m);
    @mkdir($o);
    $d = $o . '/' . sha1_file($t) . '.jpeg';
    if (move_uploaded_file($t, $d)) {
      header('Location: ' . $d, true, 302);
      $e = false;
    }
  }
}
if ($e) {
header('content-type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html>
<head>
  <meta charset=utf-8>
  <title>publish-pic</title>
  <link rel="shortcut icon" type=image/x-icon href=data:image/x-icon;,>
</head>
<body>
  <form action=. enctype=multipart/form-data method=post>
  <input type=file name=f accept=image/jpeg>
  <input type=submit value=max100k>
  <input type=hidden name=p value="<?php @print($p);?>">
  </form>
</body>
</html><?php
}
rmdirs(strval(($m + 1) % 3));
