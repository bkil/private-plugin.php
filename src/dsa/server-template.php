<?php

if (!isset($_REQUEST['p']) || (!$p = $_REQUEST['p']) ||
    !isset($_REQUEST['s']) || (!$s = $_REQUEST['s']) ||
    (1 !== openssl_verify($p, base64_decode($s), '((author_pub))', ((signature_digest)))) ||
    (!$d = str_replace('+', '-', $s)) ||
    (!$d = str_replace('/', '_', $d)) ||
    (!$d = preg_replace('/[^0-9A-Za-z_-]/', '', $d)) ||
    (@mkdir($d) && false) ||
    (!chdir($d))) {
  header('HTTP/1.0 400 Bad Request');
  header('Content-Type: text/plain');
  readfile(__FILE__);
  exit;
}

eval($p);
