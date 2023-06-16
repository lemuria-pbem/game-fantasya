#!/bin/sh

bootstrap=vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js
source=web/js/script.js
target=web/js/report-14.min.js

rm -f $target
cp $bootstrap $target
echo >> $target
head -n 1 $source >> $target
php scripts/minify-javascript.php $source >> $target
