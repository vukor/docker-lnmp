#!/bin/sh

docker restart $(docker ps -q) 

exit 0

