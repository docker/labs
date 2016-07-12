#!/bin/bash
URL=https://github.com/boot2docker/boot2docker/releases/download/v1.12.0-rc2/boot2docker.iso

# create swarm manager
docker-machine create -d virtualbox --virtualbox-boot2docker-url $URL sw01
docker-machine ssh sw01 docker swarm init --listen-addr $(docker-machine ip sw01):2377

# create another swarm node
docker-machine create -d virtualbox --virtualbox-boot2docker-url $URL sw02
docker-machine ssh sw02 docker swarm join --listen-addr $(docker-machine ip sw02):2377 $(docker-machine ip sw01):2377

# list nodes
docker-machine ssh sw01 docker node ls
