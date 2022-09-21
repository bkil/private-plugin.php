<?php

function map_rename_html(string $str, array $map): string {
  $in = '^' . $str . '$';
  $out = '';
  list ($from, $to) = get_map_rename_from_to($map);

  while ($in) {
    if (1 !== preg_match("/^([^<]*)(<[^>]*>)(.*)$/", $in, $match))
      return map_rename_js_fin($in, $out, $map);

    $out .= $match[1];
    $todo = $match[2];
    $in = $match[3];

    if (1 === preg_match("/^(.* on[a-z]+)(=(:?(:?'[^'>]*'|\"[^\">]*\"|[^ '\">])+)[ >])/", $todo, $match)) {
      $out .= $match[1];
      $todo = $match[2];
      if (NULL === $res = preg_replace($from, $to, $todo))
        return map_rename_js_fin($in, $out . $todo, $map);
      $out .= $res;
    } else {
      $out .= $todo;
    }
  }
  return map_rename_js_fin($in, $out, $map);
}

function map_rename_js(string $str, array $map): string {
  $in = '^' . $str . '$';
  $out = '';
  list ($from, $to) = get_map_rename_from_to($map);

  while ($in) {
    if (1 !== preg_match("/^(([^'\"=(]|[=(][^\/'\"])*[=(]?)(('([^'\\\\]|\\.)*'|\"([^\"\\\\]|\\.)*\"|\/[^\/]+\/)(.*)|$)/", $in, $match))
      return map_rename_js_fin($in, $out, $map);
    $todo = $match[1];
    $skip = isset($match[4]) ? $match[4] : '';
    $in = isset($match[7]) ? $match[7] : '';
    if (NULL === $res = preg_replace($from, $to, $todo))
      return map_rename_js_fin($in, $out . $todo . $skip, $map);
    $out .= $res . $skip;
  }
  return map_rename_js_fin($in, $out, $map);
}

function map_rename_js_fin(string $in, string $out, array $map): string {
  $out = substr($out . $in, 1, -1);
  list ($from, $to) = get_map_rename_from_to_raw($map);
  $out = preg_replace($from, $to, $out);
  return $out;
}

function get_map_rename_from_to(array $map): array {
  $from = [];
  $to = [];
  foreach ($map as $record) {
    $from[] = '/\b' . $record[1] . '\b/';
    $to[] = $record[0];
  }
  return [$from, $to];
}

function get_map_rename_from_to_raw(array $map): array {
  $from = [];
  $to = [];
  foreach ($map as $record) {
    $from[] = '/[{][{]' . $record[1] . '[}][}]/';
    $to[] = $record[0];
  }
  return [$from, $to];
}
