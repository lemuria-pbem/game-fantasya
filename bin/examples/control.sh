#!/bin/sh

PROJECT=/home/lemuria/game-fantasya
OUTPUT=/tmp/control-lemuria.txt
FROM="From: user@host"
RECIPIENT=mail@example.org
SUBJECT="Lemuria report error"

cd $PROJECT > $OUTPUT 2>&1
bin/sync.sh -o >> $OUTPUT 2>&1
php $PROJECT/bin/turn.php >> $OUTPUT 2>&1
if [ $? -ne 0 ]
then
	mail -s "$SUBJECT" -a "$FROM" $RECIPIENT < $OUTPUT
fi
rm $OUTPUT
