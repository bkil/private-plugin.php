<?php
include_once '../include/format.php';

print(fullstack(get_priv_pub_key_rsa(), '../../example/voting-fs-min.php') . "\n");
print(fullstack(get_priv_pub_key_rsa(), '../../example/publish-pic-be.php', '../../example/publish-pic-fe.html') . "\n");
