<?php

include_once '../include/files.php';
include_once '../include/minify.php';
include_once '../include/openssl.php';

function main() {
  $rsa = get_priv_pub_key_rsa();
  $aes = get_aes_key();
  $p = backend('../../example/chat-fs.php', $rsa, $aes, $create_index=true);

  $host = get_target_host();
  $url = $host . '#' . $p;
  put_var('test.url', $url);
  print($url . PHP_EOL);
}

function backend($file, $rsa, $web_key, $create_index = false): string {
  $author_priv = $rsa[0];
  $author_pub = $rsa[1];
  $minified = minify_php(strip_php($file));
  $compressed = gzdeflate($minified);

  $cipher = 'AES-256-CTR';
  $ivlen = openssl_cipher_iv_length($cipher);
  $iv = openssl_random_pseudo_bytes($ivlen);
  if (!$raw = openssl_encrypt($compressed, $cipher, $web_key, $options=OPENSSL_RAW_DATA, $iv)) {
    openssl_error('openssl_encrypt');
  }
  $encrypted = $iv . $raw;
  $signature_digest = OPENSSL_ALGO_SHA256;
  if (!openssl_sign($encrypted, $signature, $author_priv, $signature_digest)) {
    openssl_error('openssl_sign');
  }

  $coded = base64_encode($signature . $encrypted);

  if ($create_index) {
    if (!file_exists('deploy') && !mkdir('deploy')) {
      err_exit('mkdir deploy');
    }
    $src = file_get_contents('server-template.php');
    $src = str_replace('((author_pub))', $author_pub, $src);
    $src = str_replace('((web_key))', base64_encode($web_key), $src);
    $src = str_replace('((signature_digest))', $signature_digest, $src);
    $src = str_replace('((signature_length))', strlen($signature), $src);
    $src = str_replace('((cipher))', $cipher, $src);
    $src = str_replace('((cipher_iv_length))', $ivlen, $src);
    $src = str_replace('((get_to_post))', strip_php('../../example/get-to-post.html'), $src);
    file_put_contents('deploy/index.php', $src);
  }

  return $coded;
}

main();
