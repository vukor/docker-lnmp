## start mysql
/usr/bin/mysqld_safe --datadir=/var/lib/mysql --socket=/var/lib/mysql/mysql.sock --pid-file=/var/run/mysqld/mysqld > /dev/null 2>&1 &

## set permissions for mysql
sleep 1
/usr/bin/mysql -e "grant all on *.* to $MYSQL_LOGIN identified by '$MYSQL_PASSWORD';"

## start php-fpm
/usr/sbin/php-fpm --daemonize -c /etc/php.ini

## start nginx
/usr/sbin/nginx -c /etc/nginx/nginx.conf

