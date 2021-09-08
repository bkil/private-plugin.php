<?php

// Matrix Element has a bug that it eats certain rich formatting characters even in URI's
// It also unencodes HTML entities like &amp; in non-preformatted context
function rawurlencode_matrix(string $s): string {
  $s = preg_replace_callback(
        '"[^][A-Za-z0-9._~!$\'(),;=:@/?\\^`{|}+-]"',
        function ($m) { return rawurlencode($m[0]); },
        $s);
  $s = str_replace('*', '%2a', $s);
  return $s;
}

function rawurlencode_unsafe(string $s): string {
  return
    preg_replace_callback(
      '"[^][A-Za-z0-9._~!$&\'()*,;=:@/?*\\^`{|}+-]"',
      function ($m) { return rawurlencode($m[0]); },
      $s);
}

function urlencode_unsafe(string $s): string {
  return
    preg_replace_callback(
      '"[^][A-Za-z0-9._~!$&\'()*,;=:@/?*\\^`{|}-]"',
      function ($m) { return urlencode($m[0]); },
      $s);
}

function minify_php(string $s, bool $single_line = true): string {
  $s = preg_replace('~//[^\n]*~', '', $s);
  return minify($s, $single_line, '[a-z0-9_]');
}

function minify_html(string $s, bool $single_line = true): string {
  return minify($s, $single_line, '[a-z0-9_/"\".{}-]');
}

function minify(string $s, bool $single_line, string $wordchars): string {
  if ($single_line)
    $s = str_replace("\n", ' ', $s);

  $s = str_replace('&', '&amp;', $s);
  $separation_regexp = "~($wordchars) ( *$wordchars)~i";
  $s = preg_replace($separation_regexp, '\1&nbsp;\2', $s);
  while ($s !== $n = preg_replace("~(^|\n)(([^\"' ]|\"[^\"]*\"|'[^']*')*) +~", '\1\2', $s))
    $s = $n;
  $s = str_replace('&nbsp;', ' ', $s);
  $s = str_replace('&amp;', '&', $s);
  $s = preg_replace('~\s*$~', '', $s);
  return $s;
}

function strip_php(string $file): string {
  $out = file_get_contents($file);
  $out = preg_replace('~^<[?]php\s*~', '', $out);
  $out = preg_replace('~\s*$~', '', $out);
  return $out;
}
