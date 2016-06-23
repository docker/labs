#!/bin/bash

# By default, 1 manager and 2 additional workers
NODES=3

# IP of manager node
MANAGER_IP=

# ID of the service deployed
SERVICE_ID=

# Number of replicas for the test service
SERVICE_REPLICAS=5

# Service port
EXPOSED_PORT=8080

function usage(){
  echo Usage: swarm.sh [-n node_number] [-r service_replicas] [-p exposed_port]
  exit 0
}

# Get options
while getopts ":n:r:p:" opt; do
  case $opt in
    n)
      NODES=$OPTARG
      ;;
    r)
      SERVICE_REPLICAS=$OPTARG
      ;;
    p)
      EXPOSED_PORT=$OPTARG
      ;;
    \?)
      usage
      ;;
  esac
done

# Get number of workers
WORKERS=$((NODES-1))

echo "-> swarm will start with 1 manager and $WORKERS workers"

# Create Docker host for manager
function create_manager {
  echo "-> creating Docker host for manager (please wait)"
  docker-machine create --driver virtualbox manager 1>/dev/null
  MANAGER_IP=$(docker-machine ip manager)
}

# Create Docker host for workers
function create_workers {
  for i in $(seq 1 $WORKERS); do
    echo "-> creating Docker host for worker $i (please wait)"
    docker-machine create --driver virtualbox worker$i 1>/dev/null
  done
}

# Init swarm
function init_swarm {
  echo "-> init swarm"
  docker-machine ssh manager docker swarm init --listen-addr $MANAGER_IP:2377
}

# Join worker to the party
function join_workers {
  for i in $(seq 1 $WORKERS);do
    echo "-> join worker to the swarm"
    WORKER_IP=$(docker-machine ip worker$i)
    docker-machine ssh worker$i docker swarm join --listen-addr $WORKER_IP:2377 $MANAGER_IP:2377
  done
}

# Deploy a small test application (http server) as a service
function deploy_service {
  echo "-> deploy service with $SERVICE_REPLICAS replicas with exposed port $EXPOSED_PORT"
  SERVICE_ID=$(docker-machine ssh manager docker service create --name city --replicas $SERVICE_REPLICAS --publish "$EXPOSED_PORT:80" lucj/randomcity:1.1)
}

# Wait for service to be available
function wait_service {
  echo "-> waiting for service $SERVICE_ID to be available"

  # TASKS_NBR=$(docker-machine ssh manager docker service tasks city | grep -v 'SERVICE'  | wc -l)
  TASKS_NBR=$(docker-machine ssh manager docker service ls | grep city | awk '{print $3}' | cut -d '/' -f1)

  while [ "$TASKS_NBR" -lt "$SERVICE_REPLICAS" ]; do 
    echo "... retrying in 2 seconds"
    sleep 2
    # TASKS_NBR=$(docker-machine ssh manager docker service tasks city | grep -v 'SERVICE'  | wc -l)
    TASKS_NBR=$(docker-machine ssh manager docker service ls | grep city | awk '{print $3}' | cut -d '/' -f1)
  done
}

# Display status
function status {
  echo "-> service available on http://$MANAGER_IP:$EXPOSED_PORT"

  docker-machine ssh manager docker service ls
  docker-machine ssh manager docker service tasks city

}

function main {
  create_manager
  create_workers
  init_swarm
  join_workers
  deploy_service
  wait_service
  status
}

main
