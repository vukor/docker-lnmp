#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

## set correct permissions
chown -R `id -u`:`id -g` "$DIR"/../www/ "$DIR"/../logs/

docker run -d \
	-e MYSQL_LOGIN="" \
	-e MYSQL_PASSWORD="" \
	-p 80:80 \
	-p 3306:3306 \
	-v "$DIR"/../etc/nginx/nginx.conf:/etc/nginx/nginx.conf \
	-v "$DIR"/../etc/nginx/hosts/:/etc/nginx/hosts/ \
	-v "$DIR"/../etc/php-fpm.conf:/etc/php-fpm.conf \
	-v "$DIR"/../etc/php-fpm.d/:/etc/php-fpm.d/ \
	-v "$DIR"/../etc/php.ini:/etc/php.ini \
	-v "$DIR"/../etc/my.cnf:/etc/my.cnf \
	-v "$DIR"/../www:/home/dev/www \
	-v "$DIR"/../logs:/home/dev/logs \
	vukor/docker-lnmp
