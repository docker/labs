#!/bin/bash
LINK=https://test.docker.com/builds/Linux/x86_64/docker-1.12.0-rc1.tgz

SIZE=2gb
REGION=ams2
IMAGE=ubuntu-15-10-x64

PREFIX=do

# create swarm manager
docker-machine create \
  --driver=digitalocean \
  --digitalocean-access-token=${DO_TOKEN} \
  --digitalocean-size=${SIZE} \
  --digitalocean-region=${REGION} \
  --digitalocean-private-networking=true \
  --digitalocean-image=${IMAGE} \
  ${PREFIX}-sw01
echo "sudo /etc/init.d/docker stop && curl $LINK | tar xzf - && sudo mv docker/* /usr/bin && rm -rf docker/ && sudo /etc/init.d/docker start" | docker-machine ssh ${PREFIX}-sw01 sh -
docker-machine ssh ${PREFIX}-sw01 docker swarm init

# create another swarm node
docker-machine create \
  --driver=digitalocean \
  --digitalocean-access-token=${DO_TOKEN} \
  --digitalocean-size=${SIZE} \
  --digitalocean-region=${REGION} \
  --digitalocean-private-networking=true \
  --digitalocean-image=${IMAGE} \
  ${PREFIX}-sw02
echo "sudo /etc/init.d/docker stop && curl $LINK | tar xzf - && sudo mv docker/* /usr/bin && rm -rf docker/ && sudo /etc/init.d/docker start" | docker-machine ssh ${PREFIX}-sw02 sh -
docker-machine ssh ${PREFIX}-sw02 docker swarm join $(docker-machine ip ${PREFIX}-sw01):2377

# list nodes
docker-machine ssh ${PREFIX}-sw01 docker node ls
