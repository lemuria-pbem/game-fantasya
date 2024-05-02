#!/bin/sh

NPC_SCRIPTS=resources/NPC
SERVER=host:path/to/game-fantasya
ROUND=$(grep '"round"' storage/config.local.json | cut -d ' ' -f 2 | cut -d ',' -f 1)

help() {
	echo sync.sh [option] - synchronisiert Spielstandsdaten
	echo Optionen:
	echo "\t-a   alles synchronisieren"
	echo
	echo "\t-c   nur config.json"
	echo "\t-g   nur /game"
	echo "\t-l   nur /log"
	echo "\t-n   nur /names"
	echo "\t-o   nur /orders"
	echo "\t-s   nur /scripts"
	echo "\t-t   nur /turn"
	echo
	echo "\t-S   NPC-Skripte hochladen"
	echo
	echo "\t-h   Hilfe anzeigen"
	exit 0
}

uploadScripts() {
	echo "Übertrage NPC-Skripte..."
	rsync -avz $NPC_SCRIPTS/ $SERVER/storage/scripts/
	exit 0
}

while getopts acghlnosSt what
do
	case $what in
		a) path=/ ;;
		c) path=/config.json ;;
		g) path=/game/$ROUND/ ;;
		h) help ;;
		l) path=/log/$ROUND/ ;;
		n) path=/names/ ;;
		o) path=/orders/$ROUND/ ;;
		s) path=/scripts/ ;;
		S) uploadScripts ;;
		t) path=/turn/$ROUND/ ;;
	esac
done

if [ -z "$path" ]
then
	help
fi

echo "Synchronisiere $path"
rsync -avz $SERVER/storage$path storage$path
