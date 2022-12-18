<?php

// Matrix Element has a bug that it eats certain rich formatting characters even in URI's
// It also unencodes HTML entities like &amp; in non-preformatted context
// Some testing with bridge round trips proves that none of `*`, `_` or `\\` are safe.
// `%` is needed for percentile escaping.
// `#` chops off the anchor
function rawurlencode_matrix(string $s): string {
  $s = preg_replace_callback(
        '*[^][A-Za-z0-9.~!$\'()+,;<=>:@/?^`{|}"&-]*',
        function ($m) { return rawurlencode($m[0]); },
        $s);
  $s = preg_fixed_point('~^([^`]*`[^`]*)`~', '\1%60', $s);
  $s = preg_replace('~<([!/]?[a-zA-Z])~', '%3c\1', $s);
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
  $s = delete_comments($s);
  return minify($s, $single_line, '[a-z0-9_]');
}

function minify_html(string $s, bool $single_line = true): string {
  // this does not handle the full grammar - do improve on demand
  $s = minify($s, $single_line, "[a-z0-9_\\.*/=()\"'-]"); // {}
  return $s;
}

function minify_js(string $s, bool $single_line = true): string {
  // this does not handle the full grammar - do improve on demand
  $s = delete_comments($s);
  $s = preg_replace('/\(\s*([a-zA-Z_][a-zA-Z_0-9]*)\s*\)\s*(=>)/', ' \1\2', $s);
  $s = minify($s, $single_line, "[a-z0-9_\\\\]");
  $s = str_replace(';}', '}', $s);
  $s = str_replace(',]', ']', $s);
  $s = preg_replace('/\b(const|let|var) ([a-zA-Z_][a-zA-Z_0-9]* *=)/', '\2', $s);
  $s = preg_replace('/\b(let|var) ([a-zA-Z_][a-zA-Z_0-9]*)(,[a-zA-Z_][a-zA-Z_0-9]*)*;/', '', $s);
  $s = preg_replace('/;$/', '', $s);
  return $s;
}

function delete_comments(string $s): string {
  return preg_replace("~(^|\n)(([^\"'/\n]|/[^/]|\"[^\"\n]*\"|'[^'\n]*')*\s+|)//[^\n]*~", '\1\2', $s);
}

function minify(string $s, bool $single_line, string $wordchars): string {
  if ($single_line)
    $s = str_replace("\n", ' ', $s);

  $s = str_replace('&', '&amp;', $s);
  $s = preg_fixed_point("~(/[^/ a-z]*)\"([^/ a-z]*/)~", '\1&quot;\2', $s);
  $s = preg_fixed_point("~(/[^/ a-z]*)'([^/ a-z]*/)~", '\1&apos;\2', $s);
  $separation_regexp = "~($wordchars) ( *$wordchars)~i";
  $s = preg_replace($separation_regexp, '\1&nbsp;\2', $s);
  $s = preg_replace('~([0-9]) ( *\.)~', '\1&nbsp;\2', $s);
  $s = preg_replace('~(\. *) ([0-9])~', '\1&nbsp;\2', $s);
  $s = preg_fixed_point("~((?:^|\n)(?:[^\"'\n ]|\"(?:[^\"\n\\\\]|\\\\.)*\"|'[^'\n]*')*) +~", '\1', $s);
  $s = str_replace('&nbsp;', ' ', $s);
  $s = str_replace('&quot;', '"', $s);
  $s = str_replace('&apos;', "'", $s);
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
  while (($subject !== $new = preg_replace($pattern, $replacement, $subject)) &&
    ($new !== NULL))
    $subject = $new;
  if ($new === NULL) {
    $err = preg_last_error_message();
    echo "warning: $err failure in preg_fixed_point(\"$pattern\", \"$replacement\", \"$subject\")";
  }
  return $subject;
}

function preg_last_error_message(): string {
  $e = preg_last_error();
  switch ($e) {
    case PREG_NO_ERROR:
      return 'PREG_NO_ERROR';
    case PREG_INTERNAL_ERROR:
      return 'PREG_INTERNAL_ERROR';
    case PREG_BACKTRACK_LIMIT_ERROR:
      return 'PREG_BACKTRACK_LIMIT_ERROR';
    case PREG_RECURSION_LIMIT_ERROR:
      return 'PREG_RECURSION_LIMIT_ERROR';
    case PREG_BAD_UTF8_ERROR:
      return 'PREG_BAD_UTF8_ERROR';
    case PREG_BAD_UTF8_OFFSET_ERROR:
      return 'PREG_BAD_UTF8_OFFSET_ERROR';
    case 6:
      return 'PHP_PCRE_JIT_STACKLIMIT_ERROR';
    default:
      return "unknown($e)";
  }
}
