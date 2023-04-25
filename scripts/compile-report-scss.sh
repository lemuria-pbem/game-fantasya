#!/bin/sh

bootstrap=vendor/twbs/bootstrap/dist/css/bootstrap.min.css
source=web/css/style.scss
target=web/css/report-13.min.css

rm -f $target
cp $bootstrap $target
echo >> $target
sass --style compressed --no-cache --trace $source | sed '1s/^\xEF\xBB\xBF//' >> $target
