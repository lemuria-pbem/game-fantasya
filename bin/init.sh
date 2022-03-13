#!/bin/bash

GAME=lemuria
BASE_DIR=/home/fantasya/games/$GAME
BIN_DIR=$BASE_DIR/lemuria-alpha/bin
TURN=0
LOG_DIR=log
EMAIL_COMMAND="php /var/customers/webs/fantasya/website/bin/console send:lemuria -vvv"
LOG=$LOG_DIR/run.log

cd $BASE_DIR
touch $LOG

echo "Lemuria init: `date`" >> $LOG
ZAT_REPORTS=`php8.1 $BIN_DIR/init.php`
ZAT_RESULT=$?
echo "Lemuria exit code: $ZAT_RESULT" >> $LOG
if [ $ZAT_RESULT -gt 0 ]
then
	echo "Game aborted!" >> $LOG
	exit 1
fi
echo >> $LOG

echo "Sending e-mails..." >> $LOG
EMAIL_RESULT=`EMAIL_COMMAND 2>&! >> $LOG`
if [ $EMAIL_RESULT -ne 0 ]
then
	echo "Sending e-mails failed (code $EMAIL_RESULT)!" >> $LOG
fi
echo >> $LOG

echo "Lemuria init end: `date`" >> $LOG
echo "Finished." >> $LOG

# Move run log to the game log directory of this turn.
mv $LOG $LOG_DIR/$TURN/
