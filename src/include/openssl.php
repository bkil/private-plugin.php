<?php

include_once 'files.php';

function get_priv_pub_key(): array {
  $priv_file = 'var/author.priv';
  $pub_file = 'var/author.pub';
  $public_key = file_exists($pub_file) ? file_get_contents($pub_file) : false;
  $private_key = file_exists($priv_file) ? file_get_contents($priv_file) : false;
  if (!$public_key || !$private_key) {
    $config = array(
        "digest_alg" => "SHA256",
        "private_key_bits" => 1024, // 3072
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    );

    if (!$keys = openssl_pkey_new($config)) {
      err_exit('openssl_pkey_new');
    }

    if (!openssl_pkey_export($keys, $private_key)) {
      openssl_error('openssl_pkey_export');
    }

    $public_key = openssl_pkey_get_details($keys);
    $public_key = $public_key["key"];
    if (!file_put_contents($pub_file, $public_key)) {
      err_exit('file_put_contents ' . $pub_file);
    }
    if (!file_put_contents($priv_file, $private_key)) {
      err_exit('file_put_contents ' . $priv_file);
    }
  }
  return array($private_key, $public_key);
}

function get_aes_key(): string {
  create_var();
  $web_key = file_exists('var/web.key') ? file_get_contents('var/web.key') : false;
  if (!$web_key) {
    $web_key = openssl_random_pseudo_bytes(32);
    if (!file_put_contents('var/web.key', $web_key)) {
      err_exit('file_put_contents var/web.key');
    }
  }
  return $web_key;
}

function openssl_error(string $message): void {
  err_exit($message . ' - ' . openssl_error_string());
}

function err_exit(string $message): void {
  print('error: ' . $message . PHP_EOL);
  exit(1);
}
