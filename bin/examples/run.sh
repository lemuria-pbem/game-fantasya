#!/bin/bash

export LEMURIA_ZERO_HOUR=`date +%s.%N`

GAME=lemuria
BASE_DIR=/home/lemuria/games/$GAME
SRC_DIR=$BASE_DIR/game-fantasya
BIN_DIR=$SRC_DIR/bin
LAST_TURN=`php $BIN_DIR/last-turn.php`
LAST_NEWCOMERS_FILE=$SRC_DIR/storage/game/$LAST_TURN/newcomers.json
TURN=`expr $LAST_TURN + 1`
NEWCOMERS_FILE=$SRC_DIR/storage/game/$TURN/newcomers.json
EMAIL_COMMAND="php /var/customers/webs/fantasya/website/bin/console send:lemuria $GAME -vvv"
CLEAR_CACHE_COMMAND="php $BIN_DIR/simulate.php --clear-cache"
BUILD_CACHE_COMMAND="php $BIN_DIR/simulate.php --build-cache"
LOG_DIR=log
LOG=$LOG_DIR/run.log

cd $BASE_DIR
touch $LOG

echo "Lemuria ZAT start: `date`" >> $LOG
echo "Running turn $TURN..." >> $LOG
echo "Running the game..." >> $LOG
TIMER_START=`date +%s`
ZAT_REPORTS=`php $BIN_DIR/turn.php`
ZAT_RESULT=$?
echo "Lemuria exit code: $ZAT_RESULT" >> $LOG
TIMER_END=`date +%s`
let DURATION=($TIMER_END-$TIMER_START+30)/60
echo "This AW took $DURATION minutes." >> $LOG
if [ $ZAT_RESULT -gt 0 ]
then
	echo "Game aborted!" >> $LOG
	exit 1
fi
echo >> $LOG

# Allow website to write newcomers.json.
chmod go+w $NEWCOMERS_FILE
# Reset last newcomers.json.
chmod go-w $LAST_NEWCOMERS_FILE
# Send emails via website command.
echo "Sending e-mails..." >> $LOG
$EMAIL_COMMAND >> $LOG 2>&1
EMAIL_RESULT=$?
if [ $EMAIL_RESULT -ne 0 ]
then
	echo "Sending e-mails failed (code $EMAIL_RESULT)!" >> $LOG
fi
echo >> $LOG

echo "Lemuria ZAT end: `date`" >> $LOG
echo "Finished." >> $LOG

# Move run log to the game log directory of this turn.
mv $LOG $LOG_DIR/$TURN/

# Rebuild the simulation FastCache.
$CLEAR_CACHE_COMMAND >> $LOG 2>&1
$BUILD_CACHE_COMMAND >> $LOG 2>&1
