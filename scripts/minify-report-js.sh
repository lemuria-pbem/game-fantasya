#!/bin/sh

jquery=vendor/components/jquery/jquery.min.js
bootstrap=vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js
source=web/js/script.js
target=web/js/report.min.js

rm -f $target
cp $jquery $target
cat $bootstrap >> $target
echo >> $target
head -n 1 $source >> $target
php scripts/minify-javascript.php $source >> $target
