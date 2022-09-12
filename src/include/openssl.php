<?php

include_once 'files.php';

function get_priv_pub_key_dsa(): array {
  return get_priv_pub_key_generic(
    array(
      "digest_alg" => "SHA512",
      "private_key_bits" => 3072,
      "private_key_type" => OPENSSL_KEYTYPE_DSA,
    ));
}

function get_priv_pub_key_rsa(): array {
  return get_priv_pub_key_generic(
    array(
      "digest_alg" => "SHA512",
      "private_key_bits" => 1024, // 3072
      "private_key_type" => OPENSSL_KEYTYPE_RSA,
    ));
}

function get_priv_pub_key_generic($config): array {
  $pub_name = 'author.pub';
  $pub_file = 'var/' . $pub_name;
  $priv_name = 'author.priv';
  $priv_file = 'var/' . $priv_name;
  $public_key = file_exists($pub_file) ? file_get_contents($pub_file) : false;
  $private_key = file_exists($priv_file) ? file_get_contents($priv_file) : false;
  if (!$public_key || !$private_key) {
    if (!$keys = openssl_pkey_new($config)) {
      err_exit('openssl_pkey_new');
    }

    if (!openssl_pkey_export($keys, $private_key)) {
      openssl_error('openssl_pkey_export');
    }

    $public_key = openssl_pkey_get_details($keys);
    $public_key = $public_key['key'];
    put_var($pub_name, $public_key);
    put_var($priv_name, $private_key);
  }
  return array($private_key, $public_key);
}

function get_aes_key(): string {
  $web_key = file_exists('var/web.key') ? file_get_contents('var/web.key') : false;
  if (!$web_key) {
    $web_key = openssl_random_pseudo_bytes(32);
    put_var('web.key', $web_key);
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
