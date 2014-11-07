# Version: 0.0.5
FROM centos:centos6
MAINTAINER Anton Bugreev <anton@bugreev.ru>

## mysql
#RUN yum install mysql mysql-server -y
#RUN mkdir -m 770 /var/log/mysql && chown mysql:mysql /var/log/mysql
#RUN mysql_install_db
# my.cnf add

## create user dev
RUN useradd -u 1026 dev -g 100

## nginx
RUN rpm -Uvh http://nginx.org/packages/centos/6/noarch/RPMS/nginx-release-centos-6-0.el6.ngx.noarch.rpm
RUN yum install nginx -y 
ADD ./etc/nginx/nginx.conf /etc/nginx/nginx.conf

## php
RUN yum install php php-cli php-mysql php-mbstring php-gd php-fpm ImageMagick -y
ADD ./etc/php-fpm.conf /etc/php-fpm.conf
ADD ./etc/php-fpm.d/www.conf /etc/php-fpm.d/www.conf
ADD ./etc/php.ini /etc/php.ini


## Main
ADD ./start.sh /start.sh
RUN chmod 755 /start.sh

CMD ["/bin/bash", "/start.sh"]

## allow ports 
#EXPOSE 3306
EXPOSE 80

