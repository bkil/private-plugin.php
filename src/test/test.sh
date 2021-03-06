#!/bin/sh
# php -S localhost:8080 deploy/index.php

URL="$1"
[ -n "$URL" ] || {
  php linkify.php || exit $?
  URL="`cat var/test.url`"
}
HOST="`echo "$URL" | sed "s~?.*$~~"`"
PARAMS="`echo "$URL" | sed "s~^http://[^/]*/?~~ ; s~^http://[^/]*/#~p=~"`"

php \
  --syntax-check \
  "deploy/index.php" &&
curl \
  -v \
  --data "$PARAMS" \
  "$HOST"
