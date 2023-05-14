#!/bin/sh

PROJECT=/home/lemuria/game-fantasya
TURN=$1

if [ -z "$TURN" ]
then
	echo Fehler: Keine Zugnummer angegeben. >&2
	exit 1
fi

NEXT=`expr $TURN + 1`
echo Lege symbolische Links für neuen Zug Nr. $NEXT an...
cd $PROJECT || exit 1

rm -rf turn
mkdir -p turn || exit 2

cd turn
ln -s ../storage/config.json
ln -s ../storage/config.local.json
ln -s ../storage/game/$TURN game-before
ln -s ../storage/game/$NEXT game
ln -s ../storage/orders/$TURN orders
ln -s ../storage/turn/$NEXT reports
ln -s ../storage/log/$NEXT/lemuria.log
ln -s ../storage/log/$NEXT/run.log
