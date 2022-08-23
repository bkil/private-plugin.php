<?php
file_put_contents(w, $d = substr(file_get_contents(w) . date("\nD H:i ") . htmlspecialchars($_REQUEST[c]), -1e4));

echo '<!DOC' .
  "TYPE html><html><body><pre>$d</pre><form><input name=c><input type=submit><input name=s value=$s><input name=p value=\"" .
  htmlspecialchars($p) .
  '">';
