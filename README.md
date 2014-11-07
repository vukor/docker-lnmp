docker-lnmp
===========

This is a Dockerfile for build and run web-applications installed on Nginx + PHP-FPM + MySQL

How it's work
================

1. Download Dockerfile:

    ``$ git clone https://vukor@github.com/vukor/docker-lnmp.git``

2. Install docker on your system

3. Build docker image docker-lnmp:

    ``$ ./docker-build.sh``

4. Run container:

    ``$ ./docker-start.sh``

5. Open in your browser page http://localhost/


**** HELP ****

*** Files description ***

** scripts **
docker-build.sh - build image
docker-start.sh - start container
docker-stop.sh  - stop running container

** share dirs **
etc - configs files
www - web files
logs - nginx logs

** images **
_images - docker images


*** Useful docker commands ***

  - List running containers
    $ docker ps

  - Stop container
    $ docker stop ID

  - Remove container
    $ docker rm ID

  - Remove image
    $ docker rmi IMAGE

