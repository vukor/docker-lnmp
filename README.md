docker-lnmp
===========

This is a Dockerfile for build and run web-applications installed on Nginx + PHP-FPM + MySQL

How it's work
===========

1. Download Dockerfile:

    ``$ git clone https://vukor@github.com/vukor/docker-lnmp.git``

2. Install docker on your system

3. Install dw, this command will be use for manage docker container "vukor/docker-lnmp":

    ``$ cd docker-lnmp/``
    
    ``$ sudo dw install`` ( for GNU/Linux users )
    
    ``$ sudo dw-macos install`` ( for Mac OSX users)
    

4. Build docker image docker-lnmp:

    ``$ dw build``
    
   Or pull already building image:
   
    ``$ docker pull vukor/docker-lnmp``

5. Set MYSQL_LOGIN / MYSQL_PASSWORD for MySQL app in docker-lnmp/scripts/cmd_start.sh

6. Run container:

    ``$ dw start``

7. For test nginx open in your browser page http://localhost/

8. For test MySQL run on local host
 
    ``mysql -h YOUR-IP-ADDRESS -u MYSQL_LOGIN -pMYSQL_PASSWORD mysql``


Help
===========

Run dw -h for using help


share dirs
===========

``etc - configs files``

``www - web files``

``logs - nginx logs``
