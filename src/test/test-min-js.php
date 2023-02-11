<?php
include_once '../include/test.php';
include_once '../include/minify-js-parser.php';

function test_lexer() {
  return test_fun('lexer', array(
    ' { let y =  " a b" ; z = 9 ; { const x = `hi ${ y + ` u v ` } ` ; } ; }' =>
      '{A=" a b";B=9;{C=`hi ${A+` u v `} `;};}',
    ));
}

exit(test_lexer() ? 0 : 1);
