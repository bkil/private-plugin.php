<?php
include_once '../include/test.php';
include_once '../include/minify.php';

function test_rawurlencode_matrix(): bool {
  return test_fun('rawurlencode_matrix', array(
    'a' => 'a',
    'a*b' => 'a*b',
    'a*b*c' => 'a*b%2ac',
    'a*b*c*d' => 'a*b%2ac%2ad',
    'a_b_c' => 'a_b_c',
    'a__b__c' => 'a__b__c',
    '_._a_.' => '_.%5fa%5f.',
    '_._aa_.' => '_.%5faa%5f.',
    '_.__a__.' => '_.%5f_a%5f%5f.',
    '_.__aa__.' => '_.%5f_aa%5f%5f.',
    '._a_.' => '._a%5f.',
    '._aa_.' => '._aa%5f.',
    '.__a__.' => '.__a%5f%5f.',
    '.__aa__.' => '.__aa%5f%5f.',
    '[a](localhost)' => '[a%5d(localhost)',
    '`a' => '`a',
    '`a`' => '`a%60',
    '`a`b`c' => '`a%60b%60c',
  ));
}

function test_minify_php(): bool {
  return test_fun('minify_php', array(
    " \$s = '// ' ;//b//c\nexit ();\n" => "\$s='// ';exit();",
  ));
}

exit(test_rawurlencode_matrix() & test_minify_php() ? 0 : 1);
