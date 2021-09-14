<?php

function test_fun(string $fun, array $cases): bool {
  $fails = 0;
  foreach ($cases as $in => $expect) {
    $got = call_user_func($fun, $in);
    if ($got !== $expect) {
      print("$fun('$in') !== '$expect': '$got'" . PHP_EOL);
      $fails++;
    }
  }
  if ($fails) {
    printf("= $fails failures for $fun()" . PHP_EOL);
    return FALSE;
  } else {
    return TRUE;
  }
}
