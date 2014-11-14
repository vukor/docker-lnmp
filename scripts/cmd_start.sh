#!/bin/sh

## set correct permissions
chown -R `id`:`gid` PWD/www/ PWD/logs

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
docker run -d \
	-p 80:80 \
	-p 3306:3306 \
	-v "$DIR"/../etc/nginx/hosts/:/etc/nginx/hosts/ \
	-v "$DIR"/../www:/home/dev/www \
	-v "$DIR"/../logs:/home/dev/logs \
	vukor/docker-lnmp
