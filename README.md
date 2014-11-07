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
    
   Or pull already building image:
   
    ``$ docker pull vukor/docker-lnmp``

4. Run container:

    ``$ ./docker-start.sh``

5. Open in your browser page http://localhost/



Files description
================

scripts
==========

``docker-build.sh - build image``

``docker-start.sh - start container``

``docker-stop.sh  - stop running container``

share dirs
==========

``etc - configs files``

``www - web files``

``logs - nginx logs``

docker images
==========
``_images - docker images``
