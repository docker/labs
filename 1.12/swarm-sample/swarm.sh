NBR_MANAGER=3
NBR_WORKER=5
NBR_REPLICA=5
EXPOSED_PORT=8080

while [ "$#" -gt 0 ]; do
  case "$1" in
   "")
      echo "Usage: $0 [-m|--manager nbr_manager] [-w|--worker nbr_worker] [-r|--replica nbr_replica] [-p|--port exposed_port]"
      exit 1
      ;;
   --manager|-m)
      NBR_MANAGER="$2"
      shift 2
      ;;
   --worker|-w)
      NBR_WORKER="$2"
      shift 2
      ;;
   --replica|-r)
      NBR_REPLICA="$2"
      shift 2
      ;;
   --port|-p)
      EXPOSED_PORT="$2"
      shift 2
      ;;
  esac
done

echo "->  about to create a swarm with $NBR_MANAGER manager(s) and $NBR_WORKER workers"

# Create Docker host for managers
function create_manager {
  for i in $(seq 1 $NBR_MANAGER); do
    echo "-> creating Docker host for manager $i (please wait)"
    docker-machine create --driver virtualbox manager$i 1>/dev/null
  done
}

# Create Docker host for workers
function create_workers {
  for i in $(seq 1 $NBR_WORKER); do
    echo "-> creating Docker host for worker $i (please wait)"
    docker-machine create --driver virtualbox worker$i 1>/dev/null
  done
}

# Init swarm from manager1
function init_swarm {
  echo "-> init swarm"
  MANAGER1_IP=$(docker-machine ip manager1)
  docker-machine ssh manager1 docker swarm init --listen-addr $MANAGER1_IP:2377
}

# Join other managers to the cluster
function join_managers {
  MANAGER1_IP=$(docker-machine ip manager1)
  if [ "$((NBR_MANAGER-1))" -ge "1" ];then
    for i in $(seq 2 $NBR_MANAGER);do
      echo "-> join manager $i to the swarm"
      MANAGER_IP=$(docker-machine ip manager$i)
      cmd=$(docker-machine ssh manager$i docker swarm join --manager --listen-addr $MANAGER_IP:2377 $MANAGER1_IP:2377 2>&1 | grep "docker node" | cut -d'"' -f2)
      # accept from another manager
      docker-machine ssh manager1 $cmd 
    done
  fi
}

# Join worker to the cluster
function join_workers {
  MANAGER1_IP=$(docker-machine ip manager1)
  for i in $(seq 1 $NBR_WORKER);do
    echo "-> join worker $i to the swarm"
    WORKER_IP=$(docker-machine ip worker$i)
    docker-machine ssh worker$i docker swarm join --listen-addr $WORKER_IP:2377 $MANAGER1_IP:2377
  done
}

# Deploy a test service
function deploy_service {
  echo "-> deploy service with $NBR_REPLICA replicas with exposed port $EXPOSED_PORT"
  SERVICE_ID=$(docker-machine ssh manager1 docker service create --name city --replicas $NBR_REPLICA --publish "$EXPOSED_PORT:80" lucj/randomcity:1.1)
}

# Wait for service to be available
function wait_service {
  echo "-> waiting for service $SERVICE_ID to be available"

  TASKS_NBR=$(docker-machine ssh manager1 docker service ls | grep city | awk '{print $3}' | cut -d '/' -f1)

  while [ "$TASKS_NBR" -lt "$NBR_REPLICA" ]; do
    echo "... retrying in 2 seconds"
    sleep 2
    TASKS_NBR=$(docker-machine ssh manager1 docker service ls | grep city | awk '{print $3}' | cut -d '/' -f1)
  done
}

# Display status
function status {
  echo "-> service available on port $EXPOSED_PORT of any node"

  docker-machine ssh manager1 docker service ls
  docker-machine ssh manager1 docker service tasks city

}

function main {
  create_manager
  create_workers
  init_swarm
  join_managers
  join_workers
  deploy_service
  wait_service
  status
}

main
