#!/bin/bash

docker run -d -p 80:80 -v `pwd`/etc/nginx/hosts/:/etc/nginx/hosts/ -v `pwd`/www:/home/dev/www -v `pwd`/vakhr-lnmp/logs:/home/dev/logs vakhr-lnmp

exit 0

