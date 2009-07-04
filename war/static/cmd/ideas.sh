#!/bin/sh
AUTH=$1
HOST="http://localhost:8080"
if [ "$2" == "get" ]
then
    URL="ideas.json"
elif [ "$2" == "update" ]
then
    URL="ideas/$3.json"
    DATA="--data \"$4\""
elif [ "$2" == "insert" ]
then
    URL="ideas.json"
    DATA="--data \"$3\""
else
then
    URL="ideas.json"
fi
CMD="curl --user $AUTH $HOST/$URL $DATA"
echo $CMD
