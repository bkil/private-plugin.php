<?php
if (isset($_POST['u']))
  if (preg_match('~^(ht|f)tps?:~', $u = $_POST['u'])) {
    header('content-type: application/octet-stream');
    header('content-disposition: attachment; filename=tmp.bin');
    @readfile(
      $u,
      0,
      stream_context_create(
        array(
          'http' =>
            array(
              'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36',
              'header' =>
                array(
                  'accept: text/html;q=0.9,*/*;q=0.8',
                  'sec-fetch-user: ?1'
                )
            ),
          'ssl' =>
            array(
              'verify_peer' => 0
            )
        )
      )
    );
  }
