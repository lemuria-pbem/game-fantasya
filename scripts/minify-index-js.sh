#!/bin/sh

bootstrap=vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js
source=web/js/index.js
version=$(php scripts/web-version.php)
target=web/js/index-$version.min.js

echo "Generating $(basename $target)..."
rm -f $target
cp $bootstrap $target
echo >> $target
head -n 1 $source >> $target
php scripts/minify-javascript.php $source >> $target
