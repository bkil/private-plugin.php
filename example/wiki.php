<!DOCTYPE html>
<html>
  <head><title>URIpedia</title></head>
<body>
  <form action=//<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']?> method=post>
  <textarea name=o maxlength=4096 rows=20 cols=40 placeholder=URIpedia><?php
    if (isset($_POST['o']) && !isset($_POST['r'])) {
      file_put_contents('w.txt', substr(htmlspecialchars($_POST['o']), 0, 4096));
    }
    echo file_get_contents('w.txt') . date(PHP_EOL . 'h:m:s ');
  ?></textarea>
  <br/>
  <input name=p type=hidden value="<?php echo $p;?>">
  <input type=submit value=Save>
  <input name=r type=submit value=Refresh>
  </form>
</body></html>
