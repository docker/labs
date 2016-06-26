#!/bin/bash
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
  --engine-install-url=https://test.docker.com \
  ${PREFIX}-sw01
docker-machine ssh ${PREFIX}-sw01 docker swarm init

# create another swarm node
docker-machine create \
  --driver=digitalocean \
  --digitalocean-access-token=${DO_TOKEN} \
  --digitalocean-size=${SIZE} \
  --digitalocean-region=${REGION} \
  --digitalocean-private-networking=true \
  --digitalocean-image=${IMAGE} \
  --engine-install-url=https://test.docker.com \
  ${PREFIX}-sw02
docker-machine ssh ${PREFIX}-sw02 docker swarm join $(docker-machine ip ${PREFIX}-sw01):2377

# list nodes
docker-machine ssh ${PREFIX}-sw01 docker node ls
