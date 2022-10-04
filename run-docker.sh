#!/bin/sh

 docker build -t download-manager-block .; 
 docker run --rm -ti -p 80:80 download-manager-block  