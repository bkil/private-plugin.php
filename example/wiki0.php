<html><body><form method=post><textarea name=o><?php if(isset($_POST['w']))fwrite(fopen('w','w'),htmlspecialchars($_POST['o']),4096);readfile('w')?></textarea><input type=submit name=w value=submit><input type=submit value=get><input name=p value=<?php echo $p?> type=text></form></body></html>
