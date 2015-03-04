#!/bin/sh

## check running container
id=`docker ps -q`
if [ "$id" = "" ]; then
  echo "Not found running container!"
  exit 1
fi

DIR="$( cd "$( dirname "$0" )" && pwd )"
SSH_KEY="$DIR/../.ssh/id_rsa"
IP_ADDRESS=`docker ps -q | head -n1 | xargs docker inspect |grep 'IPAddress' | cut -d '"' -f 4`

## set right perms
chmod 700 "$DIR/../.ssh"
chmod 400 $SSH_KEY

## connect
ssh -i "$SSH_KEY" -l dev -q "$IP_ADDRESS"

exit 0

