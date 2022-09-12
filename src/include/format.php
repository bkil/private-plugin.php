<?php
include_once '../include/minify.php';
include_once '../include/openssl.php';
include_once '../include/files.php';

function fullstack(array $keys, string $backend, string $frontend = '', string $javascript = '', bool $create_index = true): string {
  $ps = backend($backend, $keys, $create_index);
  $p = $ps[0];
  $s = $ps[1];

  $host = get_target_host();

  if ($frontend) {
    $url = frontend($frontend, $p, $s, $host);
    if ($javascript)
      $url .= javascript($javascript, $host);
  }
  else
    $url = $p;

  $url = rawurlencode_matrix($url);

  if ($frontend) {
    $url = 'data:text/html;,' . $url;
    $url = $host . 'k.html#' . $url;
  } else {
    $url = str_replace('&', '%26', $url);
    $param_delim = strpos($host, '?') ? '&' : '?';
    $url = $host . $param_delim . 'p=' . $url . '&s=' . $s;
    $url = str_replace('+', '%2B', $url);
    $url = str_replace('%20', '+', $url);
  }

  put_var('test.url', $url);
  return $url;
}

function backend(string $file, array $keys, bool $create_index = false): array {
  $author_priv = $keys[0];
  $author_pub = $keys[1];
  $minified = minify_php(strip_php($file));
  $signature_digest = OPENSSL_ALGO_SHA512;

  if (!openssl_sign($minified, $signature, $author_priv, $signature_digest)) {
    openssl_error('openssl_sign');
  }

  if ($create_index) {
    if (!file_exists('deploy') && !mkdir('deploy')) {
      err_exit('mkdir deploy');
    }
    $src = file_get_contents('server-template.php');
    $src = str_replace('((author_pub))', preg_replace('~\s*$~', '', $author_pub), $src);
    $src = str_replace('((signature_digest))', $signature_digest, $src);
    file_put_contents('deploy/index.php', $src);
  }

  $s = preg_replace('/=*$/', '', base64_encode($signature));
  return array($minified, $s);
}

function frontend(string $file, string $p, string $s, string $host): string {
  $f = file_get_contents($file);
  $f = str_replace('((plugin_uri))', rawurlencode_unsafe($p), $f);
  $f = str_replace('((signature))', $s, $f);
  $f = str_replace('((form_action))', $host, $f);
  $f = minify_html($f);

  $f = str_replace('((plugin_entity))', htmlspecialchars($p), $f); // must not minify or handle `&quot;`

  return $f;
}

function javascript(string $file, string $host): string {
  $s = file_get_contents($file);
  $s = str_replace('((form_action))', $host, $s);
  $s = minify_js($s);
  $s = '<script>' . $s . '</script>';
  return $s;
}
