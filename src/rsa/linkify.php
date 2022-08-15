<?php

include_once '../include/files.php';
include_once '../include/minify.php';
include_once '../include/openssl.php';

function main() {
  $keys = get_priv_pub_key();
  $host = get_target_host();
  $ps = backend('../../example/voting-fs.php', $keys, $create_index=true);
//  $ps = backend('../../example/publish-pic-be.php', $keys, $create_index=true);
  $p = $ps[0];
  $s = $ps[1];

//  $f = frontend('../../example/publish-pic-fe.html', $p, $s, $host);
//  $data_uri = 'data:text/html;,' . rawurlencode_matrix($f);
//  $surl = $host . 'k.html#' . $data_uri;
  $surl = str_replace('+', '%2B', $host . '?p=' . rawurlencode_matrix($p) . '&s=' . $s);
  print($surl . PHP_EOL);

//  $iframe = 'document.getElementsByTagName(\'body\')[0].innerHTML="<iframe width=100% height=200px src=\"' . $data_uri . '\"></iframe>";';
//  $iurl = $host . 'j.html#' . rawurlencode_matrix($iframe);
//  print($iurl . PHP_EOL);

  put_var('test.url', $surl);
}

function backend($file, $keys, $create_index = false): array {
  $author_priv = $keys[0];
  $author_pub = $keys[1];
  $minified = minify_php(strip_php($file));
  $signature_digest = OPENSSL_ALGO_SHA256;

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

  return array($minified, base64_encode($signature));
}

function frontend(string $file, string $p, string $s, string $action): string {
  $f = file_get_contents($file);
  $f = str_replace('((plugin_uri))', rawurlencode_unsafe($p), $f);
  $f = str_replace('((signature))', $s, $f);
  $f = str_replace('((form_action))', $action, $f);
  $f = minify_html($f);

  $f = str_replace('((plugin_entity))', htmlspecialchars($p), $f); // must not minify or handle `&quot;`

  return $f;
}

main();
