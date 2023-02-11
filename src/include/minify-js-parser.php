<?php

function lexer(string $in): string {
  $global = [];
  $scope = [[]];
  $len = strlen($in);
  $keepMemberL = [
    'add',
    'appendChild',
    'assign', //
    'body', //
    'classList',
    'className',
    'createElement', //
    'createObjectStore',
    'createObjectURL',
    'createTBody',
    'createTHead',
    'createTextNode',
    'concat',
    'contains',
    'data', // the chosen key in indexedDb
    'dataset',
    'delete',
    'documentElement',
    'display', //
    'encode',
    'endsWith',
    'entries', //
    'errorCode',
    'exec',
    'filter',
    'find',
    'forEach',
    'from', //
    'fromCodePoint', //
    'fromEntries', //
    'focus',
    'get',
    'getElementsByClassName',
    'getElementsByTagName',
    'getTime',
    'hash', //
    'head', //
    'href',
    'indexOf',
    'innerText',
    'innerHTML',
    'insertCell',
    'insertRow',
    'join',
    'keys', //
    'lastIndexOf',
    'length',
    'location', //
    'log', //
    'map',
    'match',
    'matchAll',
    'objectStore',
    'onclick',
    'onenter', //
    'onerror',
    'onhashchange',
    'onkeydown',
    'onload',
    'onsuccess',
    'ontimeout',
    'onupgradeneeded',
    'open',
    'parentElement',
    'push',
    'put',
    'querySelector',
    'querySelectorAll',
    'reduce',
    'rel',
    'remove',
    'removeAttribute',
    'removeChild',
    'replace',
    'replaceAll',
    'responseText',
    'result', //
    'revokeObjectURL',
    'send',
    'slice',
    'sort',
    'split',
    'startsWith',
    'style',
    'target', //
    'test',
    'textContent',
    'timeout',
    'toISOString',
    'toLocaleString',
    'toLocaleLowerCase',
    'toString',
    'transaction',
    'trim',
    'scrollIntoView',
    'set',
    'substr',
    'value',
    'values', //
    ];
  $keepMember = [];
  for ($i = 0; $i < count($keepMemberL); $i++) {
    $keepMember[$keepMemberL[$i]] = 1;
  }

  $keepVarL = [
    'Array',
    'Blob',
    'break',
    'console',
    'continue',
    'Date',
    'decodeURIComponent',
    'delete',
    'eval',
    'document',
    'else',
    'false',
    'for',
    'function',
    'if',
    'in',
    'indexedDB',
    'isNaN',
    'new',
    'null',
    'Number',
    'Object',
    'of',
    'Option',
    'RegExp',
    'return',
    'setTimeout',
    'String',
    'TextEncoder',
    'this',
    'true',
    'Uint32Array',
    'Uint8Array',
    'undefined',
    'URL',
    'URLSearchParams',
    'while',
    'window',
    'XMLHttpRequest'
  ];
  $keepVar = [];
  for ($i = 0; $i < count($keepVarL); $i++) {
    $keepVar[$keepVarL[$i]] = 1;
  }

  $reserved = array_merge($keepMember, $keepVar);

  $name = '';
  $o = '';
  $s = 0;
  $indent = 0;
  $nested = [];
  $varIndex = 0;
  $isMember = false;
  $isSeparate = true;
  for ($i = 0; $i < $len; $i++) {
    $c = $in[$i];

    $s0Def = function () use ($c, &$s, &$o, &$name, &$indent, &$nested, &$scope, &$isMember, &$isSeparate) {
      if (isCharSpace($c)) {
      } else if ($c === 'c') {
        $s = 20;
      } else if ($c === 'l') {
        $s = 30;
      } else if (isCharJSVarFirst($c)) {
        $s = 1;
        $name = $c;
      } else {
        if ($c === '/') {
          if ($isSeparate) {
            $s = 6;
          } else {
            $s = 14;
          }
        } else {
          if ($c === '"') {
            $s = 2;
          } else if ($c === "'") {
            $s = 3;
          } else if ($c === '`') {
            $s = 4;
          } else if (isCharDigit($c)) {
            $s = 15;
          } else if ($c === '.') {
            $isMember = true;
          } else if ($c === '{') {
            $indent++;
            $scope[$indent] = [];
          } else if ($c === '}') {
            unset($scope[$indent]);
            $indent--;
            if (!empty($nested)) {
              if ($nested[count($nested) - 1] === $indent) {
                array_pop($nested);
                $s = 4;
              }
            }
          }
          $o .= $c;
        }
        $isSeparate = true;
      }
    };

    $s1Name = function () use ($reserved, $keepMember, $keepVar, &$o, &$name, &$scope, &$global, &$varIndex, &$isMember, &$isSeparate) {
      if (!$isSeparate) {
        $o .= ' ';
      }
      $isSeparate = false;
      if ($isMember) {
        $isMember = false;
        if (isset($keepMember[$name])) {
          $o .= $name;
          return;
        }
      } else if (isset($keepVar[$name])) {
        $o .= $name;
        return;
      }

      $newName = $global[$name] ?? null;
      if (!$newName) {
        $newName = getScopeVar($scope, $name);
        if (!$newName) {
          $newName = varName($varIndex, $reserved);
          $global[$name] = $newName;
        }
      }
      $o .= $newName;
    };

    $s1Def = function () use ($reserved, $s0Def, $s1Name, $c, &$s, &$o, &$name, &$indent, &$nested, &$scope, &$global, &$varIndex, &$isMember, &$isSeparate) {
      if (isCharJSVarRest($c)) {
        $name .= $c;
      } else {
        $s = 0;
        $s1Name();
        $s0Def();
      }
    };

    $s26Name = function() use ($reserved, &$o, $name, &$scope, &$varIndex, &$isMember) {
      $isMember = false;
      $newName = varName($varIndex, $reserved);
      $scope[count($scope) - 1][$name] = $newName;
      $o .= $newName;
    };

    switch ($s) {
      case 0:
        $s0Def();
        break;

      case 1:
        $s1Def();
        break;

      case 2:
        if ($c === '"') {
          $s = 0;
        }
        $o .= $c;
        break;

      case 3:
        if ($c === "'") {
          $s = 0;
        }
        $o .= $c;
        break;

      case 4:
        if ($c === '`') {
          $s = 0;
        } else if ($c === '$') {
          $s = 5;
        }
        $o .= $c;
        break;

      case 5:
        if ($c === '`') {
          $s = 0;
        } else if ($c === '{') {
          $s = 0;
          array_push($nested, $indent);
          $indent++;
          $scope[$indent] = [];
        } else {
          $s = 4;
        }
        $o .= $c;
        break;

      case 6:
        if ($c === '/') {
          $s = 7;
        } else if ($c === "\\") {
          $s = 10;
          $o .= $c;
        } else if ($c === "[") {
          $s = 11;
          $o .= $c;
        } else {
          $s = 8;
          $o .= '/' . $c;
        }
        break;

      case 7:
        if ($c === "\n") {
          $s = 0;
        }
        break;

      case 8:
        if ($c === '/') {
          $s = 9;
        } else if ($c === "[") {
          $s = 11;
        } else if ($c === "\\") {
          $s = 10;
        }
        $o .= $c;
        break;

      case 9:
        if (isCharJSVarFirst($c)) {
          $o .= $c;
        } else {
          $s = 0;
          $s0Def();
        }
        break;

      case 10:
        $o .= $c;
        $s = 8;
        break;

      case 11:
        if ($c === "\\") {
          $s = 13;
        } else {
          $s = 12;
        }
        $o .= $c;
        break;

      case 12:
        if ($c === ']') {
          $s = 8;
        } else if ($c === "[") {
          $s = 11;
        } else if ($c === "\\") {
          $s = 10;
        }
        $o .= $c;
        break;

      case 13:
        $o .= $c;
        $s = 11;
        break;

      case 14:
        if ($c === '/') {
          $s = 7;
        } else {
          $s = 0;
          $s0Def();
        }
        break;

      case 15:
        if ($c === 'x') {
          $s = 16;
          $o .= $c;
        } else if (isCharDigit($c) || ($c === '.')) {
          $s = 17;
          $o .= $c;
        } else {
          $s = 0;
          $s0Def();
        }
        break;

      case 16:
        if (isCharHex($c) || ($c === '.')) {
          $o .= $c;
        } else {
          $s = 0;
          $s0Def();
        }
        break;

      case 17:
        if (isCharDigit($c) || ($c === '.')) {
          $o .= $c;
        } else {
          $s = 0;
          $s0Def();
        }
        break;

      case 20:
        if ($c === 'o') {
          $s = 21;
        } else {
          $s = 1;
          $name = 'c';
          $s1Def();
        }
        break;

      case 21:
        if ($c === 'n') {
          $s = 22;
        } else {
          $s = 1;
          $name = 'co';
          $s1Def();
        }
        break;

      case 22:
        if ($c === 's') {
          $s = 23;
        } else {
          $s = 1;
          $name = 'con';
          $s1Def();
        }
        break;

      case 23:
        if ($c === 't') {
          $s = 24;
        } else {
          $s = 1;
          $name = 'cons';
          $s1Def();
        }
        break;

      case 24:
        if (isCharSpace($c)) {
          $s = 25;
        } else {
          $s = 1;
          $name = 'const';
          $s1Def();
        }
        break;

      case 25;
        if (isCharJSVarFirst($c)) {
          $s = 26;
          $name = $c;
        } else {
          $s = 0;
          $s0Def();
        }
        break;

// TODO: const [x, y, ...z] = [];
      case 26:
        if (isCharJSVarRest($c)) {
          $name .= $c;
        } else {
          $s = 0;
          $s26Name();
          $s0Def();
        }
        break;

      case 30:
        if ($c === 'e') {
          $s = 31;
        } else {
          $s = 1;
          $name = 'l';
          $s1Def();
        }
        break;

      case 31:
        if ($c === 't') {
          $s = 32;
        } else {
          $s = 1;
          $name = 'le';
          $s1Def();
        }
        break;

      case 32:
        if (isCharSpace($c)) {
          $s = 25;
        } else {
          $s = 1;
          $name = 'let';
          $s1Def();
        }
        break;
    }
  }

  switch ($s) {
    case 1:
      $s1Name();
      break;
    case 26:
      $s26Name();
      break;
    case 20:
      $name = 'c';
      $s1Def();
    case 21:
      $name = 'co';
      $s1Def();
    case 22:
      $name = 'con';
      $s1Def();
    case 23:
      $name = 'cons';
      $s1Def();
    case 24:
      $name = 'const';
      $s1Def();
    case 30:
      $name = 'l';
      $s1Def();
    case 31:
      $name = 'le';
      $s1Def();
    case 32:
      $name = 'let';
      $s1Def();
  }

  return $o;
}

function isCharJSVarFirst(string $c): bool {
  return ($c >= 'a') && ($c <= 'z') || ($c >= 'A') && ($c <= 'Z') || ($c === '_');
}

function isCharJSVarRest(string $c): bool {
  return isCharJSVarFirst($c) || isCharDigit($c);
}

function isCharDigit(string $c): bool {
  return ($c >= '0') && ($c <= '9');
}

function isCharHex(string $c): bool {
  return isCharDigit($c) || ($c >= 'a') && ($c <= 'f') || ($c >= 'A') && ($c <= 'F');
}

function isCharSpace(string $c): bool {
  return ($c === ' ') || ($c === "\t") || ($c === "\n");
}

function getScopeVar(array $scope, string $name) {
  for ($i = count($scope) - 1; $i >= 0; $i--) {
    $result = $scope[$i][$name] ?? null;
    if ($result) {
      return $result;
    }
  }
  return null;
}

function varName(int &$varIndex, array $reserved) {
  do {
    $s = getName($varIndex);
    $varIndex++;
  } while (isset($reserved[$s]));
  return $s;
}

function getName($i) {
  $s = '';
  $base = 52;
  do {
    $c = base64_encode(chr(0) . chr(0) . chr($i % $base))[3];
    $s .= $c;
    $i = intval($i / $base);
    $base = 62;
  } while ($i);
  return $s;
}
