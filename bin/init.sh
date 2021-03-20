#!/bin/bash

HOST=localhost
DATABASE_USER=
USER_USER=
PASSWORD_USER=''
GAME=lemuria
GAME_ID=4
BASE_DIR=/home/fantasya/games/$GAME
BIN_DIR=$BASE_DIR/lemuria-alpha/bin
TURN=0
ZIP_DIR=zip
LOG_DIR=log
EMAIL_DIR=email
EMAIL_SUBJECT="Lemuria AW $TURN"
EMAIL_TEMPLATE=$EMAIL_DIR/turn.email.template
EMAIL_TEXT=$EMAIL_DIR/turn.email.txt
EMAIL_LINK='https://www.fantasya-pbem.de/report/t'
FANTASYACOMMAND="php8.0 /var/customers/webs/fantasya/website/bin/console"
EMAIL_LOG=$EMAIL_DIR/log/$TURN
LOG=$LOG_DIR/run-$TURN.log

which b36 > /dev/null
if [ "$?" -gt 0 ]
then
	echo "b36 tool not found."
	exit 1
fi

cd $BASE_DIR
touch $LOG

echo "Lemuria init: `date`" >> $LOG
ZAT_REPORTS=`php8.0 $BIN_DIR/init.php`
ZAT_RESULT=$?
echo "Lemuria exit code: $ZAT_RESULT" >> $LOG
if [ $ZAT_RESULT -gt 0 ]
then
	echo "Game aborted!" >> $LOG
	exit 1
fi
echo >> $LOG

echo "Sending e-mails..." >> $LOG
mkdir -p $EMAIL_LOG
for REPORT_LINE in $ZAT_REPORTS
do
	PARTY=`echo $REPORT_LINE | cut -d : -f 1`
	ID=`b36 -d $PARTY`
	UUID=`echo $REPORT_LINE | cut -d : -f 2`
	REPORT=`echo $REPORT_LINE | cut -d : -f 3`
	EMAIL=`$FANTASYACOMMAND email:lemuria $UUID`
	if [ $? -eq 0 ]
	then
		WITH_ATTACHMENT=`mysql -N -s -h $HOST -u $USER_USER -D $DATABASE_USER -p$PASSWORD_USER -e "SELECT u.flags & 1 FROM user u JOIN assignment a ON a.user_id = u.id WHERE a.uuid = '$UUID'"`
		EMAIL_TOKEN=`$FANTASYACOMMAND download:token $GAME_ID $ID $EMAIL $TURN`
		if [ $? -eq 0 ]
		then
			cat $EMAIL_TEMPLATE > $EMAIL_TEXT
			echo "$EMAIL_LINK/$EMAIL_TOKEN" >> $EMAIL_TEXT
			echo "$ID -> $EMAIL" >> $LOG
			if [ $WITH_ATTACHMENT -eq 1 ]
			then
				echo "$REPORT -> $EMAIL" >> $LOG
				mutt -F $EMAIL_DIR/muttrc -s "$EMAIL_SUBJECT" -a $REPORT -- $EMAIL < $EMAIL_TEXT 2>&1 >> $LOG
			else
				mutt -F $EMAIL_DIR/muttrc -s "$EMAIL_SUBJECT" -- $EMAIL < $EMAIL_TEXT 2>&1 >> $LOG
			fi
			echo $(cat $EMAIL_TEXT) > $EMAIL_LOG/$EMAIL.mail 2>> $LOG
		else
			echo "Creation of download token failed for $EMAIL! No mail sent." >> $LOG
		fi
	else
		echo "Fetching email address failed for $UUID! No mail sent." >> $LOG
	fi
done
echo >> $LOG

echo "Lemuria init end: `date`" >> $LOG
echo "Finished." >> $LOG
