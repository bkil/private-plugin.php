<?php
function main() {
  $keys = get_keys();
  $p = publish('example/publish-pic.php', $keys, $create_index=true);

  $host = str_replace(PHP_EOL, '', file_get_contents('var/hostname.txt'));
  $url = $host . '#' . $p;
  file_put_contents('var/test.url', $url);
  print($url . PHP_EOL);
}

function get_keys(): array {
  if (!file_exists('var') && !mkdir('var')) {
    err_exit('mkdir var');
  }
  $author = get_priv_pub_key('var/author.priv', 'var/author.pub');

  $web_key = file_exists('var/web.key') ? file_get_contents('var/web.key') : false;
  if (!$web_key) {
    $web_key = openssl_random_pseudo_bytes(32);
    if (!file_put_contents('var/web.key', $web_key)) {
      err_exit('file_put_contents var/web.key');
    }
  }
  return array($author[0], $author[1], $web_key);
}

function publish($file, $keys, $create_index = false): string {
  $author_priv = $keys[0];
  $author_pub = $keys[1];
  $web_key = $keys[2];
  $compressed = gzdeflate(strip_php($file));

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
  if (!file_exists('var/hostname.txt')) {
    file_put_contents('var/hostname.txt', 'http://localhost:8080/');
  }

  if ($create_index) {
    if (!file_exists('deploy') && !mkdir('deploy')) {
      err_exit('mkdir deploy');
    }
    $src = file_get_contents('private-plugin.template.php');
    $src = str_replace('{{author.pub}}', $author_pub, $src);
    $src = str_replace('{{web.key}}', base64_encode($web_key), $src);
    $src = str_replace('{{signature_digest}}', $signature_digest, $src);
    $src = str_replace('{{signature_length}}', strlen($signature), $src);
    $src = str_replace('{{cipher}}', $cipher, $src);
    $src = str_replace('{{cipher_iv_length}}', $ivlen, $src);
    $src = str_replace('{{get_to_post}}', strip_php('example/get-to-post.html'), $src);
    file_put_contents('deploy/index.php', $src);
  }

  return $coded;
}

function strip_php(string $file): string {
  $out = file_get_contents($file);
  $out = preg_replace('~^<[?]php\s*~', '', $out);
  $out = preg_replace('~\s*$~', '', $out);
  return $out;
}

function get_priv_pub_key(string $priv_file, string $pub_file): array {
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

function openssl_error(string $message): void {
  err_exit($message . ' - ' . openssl_error_string());
}

function err_exit(string $message): void {
  print('error: ' . $message . PHP_EOL);
  exit(1);
}

main();
