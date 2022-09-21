<!DOCTYPE html>
<html>
<head>
  <title>URIpedia</title>
  <link rel="shortcut icon" type=image/x-icon href=data:image/x-icon;,>
</head>
<body>
  <form method=post action=.>
  <label for=o>Share your knowledge here</label>
  <br>
  <textarea id=o name=o maxlength=4096 rows=20 cols=40><?php
    if (isset($_POST['o']) && isset($_POST['w'])) {
      file_put_contents('w.txt', substr(htmlspecialchars($_POST['o']), 0, 4096));
    }
    readfile('w.txt');
    echo date(PHP_EOL . 'D H:i ');
  ?></textarea>
  <br/>
  <input type=submit name=w value=Save>
  <input type=submit value=Refresh>
  <input type=hidden name=p value="<?php echo $p;?>">
  </form>
</body>
</html>
