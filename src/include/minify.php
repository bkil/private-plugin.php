<?php

// Matrix Element has a bug that it eats certain rich formatting characters even in URI's
// It also unencodes HTML entities like &amp; in non-preformatted context
function rawurlencode_matrix(string $s): string {
  $s = preg_replace_callback(
        '"[^][A-Za-z0-9.~!$\'(),;=:@/?\\^{|}+`_*-]"',
        function ($m) { return rawurlencode($m[0]); },
        $s);
  $s = preg_fixed_point('~^([^`]*`[^`]*)`~', '\1%60', $s);

  $s = escape_matrix_asterisk($s);
  $s = escape_matrix_underscore($s);

  $s = str_replace('](', '%5d(', $s);
  return $s;
}

function escape_matrix_underscore(string $s): string {
// return str_replace('_', '%5f', $s);

  $W = '[^_0-9a-zA-Z]';
  $s1 = preg_fixed_point("~(^|$W)_(_*\S(.*\S)?_+(\$|$W))~", '\1%5f\2', $s);
  $s2 = preg_fixed_point("~((^|$W)_+(\S.*)?\S_*)_(\$|$W)~", '\1%5f\4', $s);
  if (strlen($s1) < strlen($s2))
    return $s1;
  else
    return $s2;
}

function escape_matrix_asterisk(string $s): string {
  return preg_fixed_point('~^([^*]*[*][^*]*)[*]~', '\1%2a', $s);
}

function rawurlencode_unsafe(string $s): string {
  return
    preg_replace_callback(
      '"[^][A-Za-z0-9._~!$&\'()*,;=:@/?\\^`{|}+-]"',
      function ($m) { return rawurlencode($m[0]); },
      $s);
}

function urlencode_unsafe(string $s): string {
  return
    preg_replace_callback(
      '"[^][A-Za-z0-9._~!$&\'()*,;=:@/?\\^`{|}-]"',
      function ($m) { return urlencode($m[0]); },
      $s);
}

function minify_php(string $s, bool $single_line = true): string {
  // this does not handle the full grammar - do improve on demand
  $s = preg_replace("~(^|\n)(([^\"'/\n]|/[^/]|\"[^\"\n]*\"|'[^'\n]*')*)//[^\n]*~", '\1\2', $s);
  return minify($s, $single_line, '[a-z0-9_]');
}

function minify_html(string $s, bool $single_line = true): string {
  // this does not handle the full grammar - do improve on demand
  return minify($s, $single_line, '[a-z0-9_/"\".{}=*-]');
}

function minify(string $s, bool $single_line, string $wordchars): string {
  if ($single_line)
    $s = str_replace("\n", ' ', $s);

  $s = str_replace('&', '&amp;', $s);
  $separation_regexp = "~($wordchars) ( *$wordchars)~i";
  $s = preg_replace($separation_regexp, '\1&nbsp;\2', $s);
  $s = preg_fixed_point("~(^|\n)(([^\"'\n ]|\"[^\"\n]*\"|'[^'\n]*')*) +~", '\1\2', $s);
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

function preg_fixed_point(string $pattern, string $replacement, string $subject): string {
  while ($subject !== $new = preg_replace($pattern, $replacement, $subject))
    $subject = $new;
  return $subject;
}
