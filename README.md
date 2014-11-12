docker-lnmp
===========

This is a Dockerfile for build and run web-applications installed on Nginx + PHP-FPM + MySQL

How it's work
================

1. Download Dockerfile:

    ``$ git clone https://vukor@github.com/vukor/docker-lnmp.git``

2. Install docker on your system

3. Build docker image docker-lnmp:

    ``$ ./dw build``
    
   Or pull already building image:
   
    ``$ docker pull vukor/docker-lnmp``

4. Run container:

    ``$ ./dw start``

5. Open in your browser page http://localhost/



Files description
================

scripts
==========

``dw build   - build image``

``dw start   - start container``

``dw stop    - stop running container``

``dw reload  - restart running container``

``dw debug   - start container in debug mode (run only bash)``

``dw install - make system link on current file (need admin privilegies)``


share dirs
==========

``etc - configs files``

``www - web files``

``logs - nginx logs``

docker images
==========
``_images - docker images``
