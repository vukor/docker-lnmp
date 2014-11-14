## start mysql
/usr/bin/mysqld_safe --datadir=/var/lib/mysql --socket=/var/lib/mysql/mysql.sock --pid-file=/var/run/mysqld/mysqld > /dev/null 2>&1 &

## start php-fpm
/usr/sbin/php-fpm --daemonize -c /etc/php.ini

## start nginx
/usr/sbin/nginx -c /etc/nginx/nginx.conf

