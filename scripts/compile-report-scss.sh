#!/bin/sh

bootstrap=vendor/twbs/bootstrap/dist/css/bootstrap.min.css
source=web/css/style.scss
target=web/css/report-12.min.css

rm -f $target
cp $bootstrap $target
echo >> $target
sass --style compressed --no-cache $source >> $target
