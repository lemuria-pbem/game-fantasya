#!/bin/bash

PROJECT=/home/lemuria/game-fantasya

cd $PROJECT

ROUND=$(grep '"round"' storage/config.local.json | cut -d ' ' -f 2 | cut -d , -f 1)
NEXT=`expr $ROUND + 1`
echo Runde $ROUND
ls -ltr storage/orders/$ROUND
head storage/log/$NEXT/lemuria.log
tail storage/log/$NEXT/lemuria.log
grep -v 'lemuria\.DEBUG' storage/log/$NEXT/lemuria.log
