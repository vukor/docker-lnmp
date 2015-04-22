#!/bin/sh

DIR="$( cd "$( dirname "$0" )" && pwd )"

## create docker-data container if does not exist
( docker ps -a |grep 'docker-data' >/dev/null ) || ( docker run --name docker-data -i -t vukor/docker-data )

## delete docker-lnmp container
( docker ps -a |grep 'docker-lnmp' >/dev/null ) && ( docker rm docker-lnmp )

## start docker-lnmp container
docker run --name=docker-lnmp -t -i --rm=true \
	-p 80:80 \
	-p 443:443 \
	-p 3306:3306 \
	-e MYSQL_LOGIN="test" \
	-e MYSQL_PASSWORD="test" \
	--volumes-from docker-data \
	-v "$DIR"/../etc/nginx/nginx.conf:/etc/nginx/nginx.conf \
	-v "$DIR"/../etc/nginx/hosts/:/etc/nginx/hosts/ \
	-v "$DIR"/../etc/php-fpm.conf:/etc/php-fpm.conf \
	-v "$DIR"/../etc/php-fpm.d/:/etc/php-fpm.d/ \
	-v "$DIR"/../etc/php.ini:/etc/php.ini \
	-v "$DIR"/../etc/my.cnf:/etc/my.cnf \
	-v "$DIR"/../www:/home/dev/www \
	-v "$DIR"/../etc/postfix/main.cf:/etc/postfix/main.cf \
	-v "$DIR"/../logs:/home/dev/logs \
	-v "$DIR"/../.ssh:/home/dev/.ssh \
	vukor/docker-lnmp /bin/bash
