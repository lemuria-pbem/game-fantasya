#!/bin/sh

bootstrap=vendor/twbs/bootstrap/dist/css/bootstrap.min.css
source=web/css/style.scss
version=$(php scripts/web-version.php)
target=web/css/report-$version.min.css

echo "Generating $(basename $target)..."
rm -f $target
cp $bootstrap $target
echo >> $target
sass --style compressed --no-cache --trace $source | sed '1s/^\xEF\xBB\xBF//' >> $target
