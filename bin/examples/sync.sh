#!/bin/sh

SERVER=host:path/to/game-fantasya

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
	echo "\t-h   Hilfe anzeigen"
	exit 0
}

while getopts acghlnost what
do
	case $what in
		a) path=/ ;;
		c) path=/config.json ;;
		g) path=/game/ ;;
		h) help ;;
		l) path=/log/ ;;
		n) path=/names/ ;;
		o) path=/orders/ ;;
		s) path=/scripts/ ;;
		t) path=/turn/ ;;
	esac
done

if [ -z "$path" ]
then
	help
fi

echo "Synchronisiere $path"
rsync -avz $SERVER/storage$path storage$path
