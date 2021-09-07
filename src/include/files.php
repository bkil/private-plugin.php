<?php

function get_target_host(): string {
  create_var();
  if (!file_exists('var/hostname.txt')) {
    file_put_contents('var/hostname.txt', 'http://localhost:8080/');
  }
  return str_replace(PHP_EOL, '', file_get_contents('var/hostname.txt'));
}

function put_var($file, $data) {
  create_var();
  return file_put_contents('var/' . $file, $data);
}

function create_var() {
  if (!file_exists('var') && !mkdir('var')) {
    err_exit('mkdir var');
  }
}
