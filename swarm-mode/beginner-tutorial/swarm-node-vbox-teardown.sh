#!/bin/bash
### Warning: This will remove all docker machines running ###
# Stop machines
docker-machine stop $(docker-machine ls -q)

# remove machines
docker-machine rm $(docker-machine ls -q)
