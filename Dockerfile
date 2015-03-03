# Version: 0.8
FROM centos:centos6
MAINTAINER Anton Bugreev <anton@bugreev.ru>

## repo
RUN yum install wget -y && cd /tmp/ && wget http://dl.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm && rpm -Uvh ./epel-release-6*rpm

## mysql
RUN yum install mysql mysql-server -y
RUN mkdir -m 770 /var/log/mysql && chown mysql:mysql /var/log/mysql
RUN mysql_install_db
ADD ./etc/my.cnf /etc/my.cnf

## create user dev
RUN useradd dev -u 1000

## nginx
RUN rpm -Uvh http://nginx.org/packages/centos/6/noarch/RPMS/nginx-release-centos-6-0.el6.ngx.noarch.rpm
RUN yum install nginx -y 
ADD ./etc/nginx/nginx.conf /etc/nginx/nginx.conf

## php
RUN yum install php php-cli php-mysql php-mbstring php-gd php-fpm php-mssql php-xml ImageMagick -y
ADD ./etc/php-fpm.conf /etc/php-fpm.conf
ADD ./etc/php-fpm.d/www.conf /etc/php-fpm.d/www.conf
ADD ./etc/php.ini /etc/php.ini

## postfix
RUN yum install postfix -y
RUN chmod 5755 /usr/sbin/postdrop /usr/sbin/postqueue

## rsyslog
RUN yum install rsyslog -y

### etc
## set timezone
RUN cp -f /usr/share/zoneinfo/Asia/Novosibirsk /etc/localtime
RUN ln -s /usr/bin/identify /usr/local/bin/identify && ln -s /usr/bin/convert /usr/local/bin/convert

## Main
ADD ./start.sh /start.sh
RUN chmod 755 /start.sh

CMD ["/bin/bash", "/start.sh"]

## allow ports 
EXPOSE 3306
EXPOSE 80

