<?php
include_once '../include/test.php';
include_once '../include/minify.php';

function test_rawurlencode_matrix(): bool {
  return test_fun('rawurlencode_matrix', array(
    'a' => 'a',
    'a_b_c' => 'a_b_c',
    'a__b__c' => 'a__b__c',
    '_._a_.' => '_._a%5f.',
    '_._aa_.' => '_._aa%5f.',
    '_.__a__.' => '_.__a%5f%5f.',
    '_.__aa__.' => '_.__aa%5f%5f.',
    '._a_.' => '._a%5f.',
    '._aa_.' => '._aa%5f.',
    '.__a__.' => '.%5f_a__.',
    '.__aa__.' => '.%5f_aa__.',
    '_a._' => '_a.%5f',
    '_a._b_' => '_a._b%5f',
    '_a._b_c' => '_a._b_c',
    'a_._b' => 'a_._b',
    'a_._b_._c' => 'a_._b%5f._c',
    '[a](localhost)' => '[a%5d(localhost)',
    '`a' => '`a',
    '`a`' => '`a%60',
    '`a`b`c' => '`a%60b%60c',
  ));
}

function test_minify_php(): bool {
  return test_fun('minify_php', array(
    " \$s = '// ' ;//b//c\nexit ();\n" => "\$s='// ';exit();",
    '"1  .  2";' => '"1  .  2";',
    '1  .  2;' => '1 . 2;',
    '$a  .  1;' => '$a. 1;',
    '1  .  $a;' => '1 .$a;',
  ));
}

exit(test_rawurlencode_matrix() & test_minify_php() ? 0 : 1);
