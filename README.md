docker-lnmp (DEPRECATED, see below section)
===========

This is a Dockerfile for build and run web-applications installed on Nginx + PHP-FPM + MySQL

How it's work
===========

1. Download project:

    ``$ git clone https://vukor@github.com/vukor/docker-lnmp.git ~/docker``

2. Install docker on your system

3. Install dw for managing docker container "vukor/docker-lnmp":

    ``$ cd ~/docker/``
    
    ``$ sudo ./dw install`` ( for GNU/Linux users )
    
    ``$ sudo ./dw-macos install`` ( for Mac OSX users )

4. Build docker image docker-lnmp:

    ``$ dw build``
    
   Or pull already building image:
   
    ``$ docker pull vukor/docker-lnmp``

5. Create mysql_data container:
 
    ``$ docker run --name docker-data -i -t vukor/docker-data``

6. Set MYSQL_LOGIN / MYSQL_PASSWORD for MySQL app in docker-lnmp/scripts/cmd_start.sh

7. Run container:

    ``$ dw start``

8. For test nginx open in your browser page http://localhost/

9. For test MySQL run on local host
 
    ``$ mysql -h YOUR-IP-ADDRESS -u MYSQL_LOGIN -pMYSQL_PASSWORD mysql``

10. For access to running container over ssh run on your host:

    ``$ dw attach``

Help
===========

Run dw -h for using help


share dirs
===========

``etc - configs files``

``www - web files``

``logs - nginx logs``


Deprecated
===========
This project is deprecated, use https://github.com/vukor/docker-web-stack
