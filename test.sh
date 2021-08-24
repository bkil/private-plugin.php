#!/bin/sh
# php -S localhost:8080 deploy/private-plugin.php

URL="$1"
[ -n "$URL" ] || URL="`cat "$(dirname "$0")/var/test.url"`"
HOST="`echo "$URL" | sed "s~?.*$~~"`"
PARAMS="`echo "$URL" | sed "s~^http://[^/]*/?~~"`"

php \
  --syntax-check \
  "deploy/private-plugin.php" &&
curl \
  -v \
  --data "$PARAMS" \
  "$HOST"
