#!/bin/bash
LINK=https://test.docker.com/builds/Linux/x86_64/docker-1.12.0-rc1.tgz

# create swarm manager
docker-machine create -d virtualbox sw01
echo "sudo /etc/init.d/docker stop && curl $LINK | tar xzf - && sudo mv docker/* /usr/local/bin && rm -rf docker/ && sudo /etc/init.d/docker start" | docker-machine ssh sw01 sh -
docker-machine ssh sw01 docker swarm init

# create another swarm node
docker-machine create -d virtualbox sw02
echo "sudo /etc/init.d/docker stop && curl $LINK | tar xzf - && sudo mv docker/* /usr/local/bin && rm -rf docker/ && sudo /etc/init.d/docker start" | docker-machine ssh sw02 sh -
docker-machine ssh sw02 docker swarm join $(docker-machine ip sw01):2377

# list nodes
docker-machine ssh sw01 docker node ls
