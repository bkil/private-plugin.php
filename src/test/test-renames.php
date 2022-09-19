<?php
include_once '../include/test.php';
include_once '../include/renames.php';

function test_map_rename_js(): bool {
  return test_fun('adapter_map_rename_js', array(
    'style=42;' => "s=42;",
    'style = 42; hash = 9;' => "s = 42; h = 9;",
    'v = " style ";' => "v = \" style \";",
    'v = \' style \';' => "v = ' style ';",
    'style = 42; "hi"; hash = 9;' => "s = 42; \"hi\"; h = 9;",
    'q=/"/;style=/"/' => "q=/\"/;s=/\"/",
  ));
}

function adapter_map_rename_js(string $str): string {
  return map_rename_js($str, array(
    array('h', 'hash'),
    array('s', 'style'),
  ));
}

function test_map_rename_html(): bool {
  return test_fun('adapter_map_rename_html', array(
    '<body onload="hash()">' => '<body onload="h()">',
    '<body onload="hash()"><img src=. onerror=post()>' => '<body onload="h()"><img src=. onerror=p()>',
  ));
}

function adapter_map_rename_html(string $str): string {
  return map_rename_html($str, array(
    array('h', 'hash'),
    array('p', 'post'),
  ));
}

exit(test_map_rename_js() & test_map_rename_html() ? 0 : 1);
