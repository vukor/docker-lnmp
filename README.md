docker-lnmp
===========

This is a Dockerfile for build and run web-applications installed on Nginx + PHP-FPM + MySQL

How it's work
================

1. Download Dockerfile:

    ``$ git clone https://vukor@github.com/vukor/docker-lnmp.git``

2. Install docker on your system

3. Build docker image docker-lnmp:

    ``$ ./scripts/cmd_build.sh``
    
   Or pull already building image:
   
    ``$ docker pull vukor/docker-lnmp``

4. Run container:

    ``$ ./scripts/cmd_start.sh``

5. Open in your browser page http://localhost/



Files description
================

scripts
==========

``cmd_build.sh - build image``

``cmd_start.sh - start container``

``cmd_stop.sh  - stop running container``

``cmd_debug.sh  - start container in debug mode (run only bash)``


share dirs
==========

``etc - configs files``

``www - web files``

``logs - nginx logs``

docker images
==========
``_images - docker images``
