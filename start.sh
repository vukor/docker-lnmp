## start rsyslog
service rsyslog start

## start mysql
service mysqld start

## set permissions for mysql
sleep 1
/usr/bin/mysql -e "grant all on *.* to $MYSQL_LOGIN identified by '$MYSQL_PASSWORD';"

## start php-fpm
service php-fpm start

## start postfix
service postfix start

## start nginx
service nginx start

