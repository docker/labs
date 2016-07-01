# Default cluster:
# - 3 manager node
# - 5 worker nodes
# - 5 replicas for the test service
# - service image: ehazlett/docker-demo
# - service port: 8080 (port exposed by the service)
# - exposed port: 8080 (port exposed to the outside)
DRIVER="virtualbox"
NBR_MANAGER=3
NBR_WORKER=5
NBR_REPLICA=5
SERVICE_IMAGE="ehazlett/docker-demo"
SERVICE_PORT=8080
EXPOSED_PORT=8080

# additional flags depending upon driver selection
ADDITIONAL_PARAMS=
PERMISSION=
PRIVATE=

# Manager and worker prefix
PREFIX=$(date "+%Y%m%dT%H%M%S")
MANAGER=${PREFIX}-manager
WORKER=${PREFIX}-worker

function usage {
  echo "Usage: $0 [--driver provider] [--amazonec2-access-key ec2_access_key] [--amazonec2-secret-key ec2_secret_key] [--amazonec2-security-group ec2_security_group] [--do_token do_token][-m|--manager nbr_manager] [-w|--worker nbr_worker] [-r|--replica nbr_replica] [-p|--port exposed_port] [--service_image service_image] [--service_port service_port]"
  exit 1
}

function error {
  echo "Error $1"
  exit 1
}

while [ "$#" -gt 0 ]; do
  case "$1" in
   --driver|-d)
      DRIVER="$2"
      shift 2
      ;;
   --manager|-m)
      NBR_MANAGER="$2"
      shift 2
      ;;
   --worker|-w)
      NBR_WORKER="$2"
      shift 2
      ;;
   --service_image)
      SERVICE_IMAGE="$2"
      shift 2
      ;;
   --service_port)
      SERVICE_PORT="$2"
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
   --digitalocean_token)
     DO_TOKEN="$2"
     shift 2
     ;;
   --amazonec2-access-key)
     EC2_ACCESS_KEY="$2"
     shift 2
     ;;
   --amazonec2-secret-key)
     EC2_SECRET_KEY="$2"
     shift 2
     ;;
   --amazonec2-security-group)
     EC2_SECURITY_GROUP="$2"
     shift 2
     ;;
   -h|--help)
      usage
      ;;
  esac
done

# Value of driver parameter's value must be among "digitalocean", "amazonec2", "virtualbox" (if no value is provided, "virtualbox" driver is used)
if [ "$DRIVER" != "virtualbox" -a "$DRIVER" != "digitalocean" -a "$DRIVER" != "amazonec2" ];then
  error "driver value must be among digitalocean, amazonec2, virtualbox"
fi

# No additional parameters needed for virtualbox driver
if [ "$DRIVER" == "virtualbox" ]; then
  echo "-> about to create a swarm with $NBR_MANAGER manager(s) and $NBR_WORKER workers on $DRIVER machines"
fi

# Make sure mandatory parameter for digitalocean driver
if [ "$DRIVER" == "digitalocean" ]; then
  ADDITIONAL_PARAMS="--digitalocean-access-token=${DO_TOKEN} --digitalocean-region=lon1 --digitalocean-size=1gb --digitalocean-image=ubuntu-14-04-x64 --engine-install-url=https://test.docker.com"
  echo "->  about to create a swarm with $NBR_MANAGER manager(s) and $NBR_WORKER workers on $DRIVER machines (lon1 / 1gb / Ubuntu 14.04)"
fi

# Make sure mandatory parameter for amazonec2 driver
if [ "$DRIVER" == "amazonec2" ];then
  if [ "$EC2_ACCESS_KEY" == "" ];then
    error "--amazonec2-access-key must be provided"
  fi
  if [ "$EC2_SECRET_KEY" == "" ];then
    error "--amazonec2-secret-key must be provided"
  fi
  if [ "$EC2_SECURITY_GROUP" == "" ];then
    error "--amazonec2-security-group must be provided (+ make sure this one allows inter hosts communication and is has opened port $EXPOSED_PORT to the outside"
  fi
  PERMISSION="sudo" 
  ADDITIONAL_PARAMS="--amazonec2-access-key ${EC2_ACCESS_KEY} --amazonec2-secret-key ${EC2_SECRET_KEY} --amazonec2-security-group ${EC2_SECURITY_GROUP} --amazonec2-security-group docker-machine --amazonec2-region eu-west-1 --amazonec2-instance-type t2.micro --amazonec2-ami ami-f95ef58a --engine-install-url=https://test.docker.com"
  echo "-> about to create a swarm with $NBR_MANAGER manager(s) and $NBR_WORKER workers on $DRIVER machines (eu-west-1 / t2.micro / Ubuntu 14.04)"
fi

echo "-> service is based on image ${SERVICE_IMAGE} exposing port ${SERVICE_PORT}"
echo "-> once deployed service will be accessible via port ${EXPOSED_PORT} to the outside"

echo -n "is that correct ? ([Y]/N)"
read build_demo

if [ "$build_demo" = "N" ]; then
  echo "aborted !"
  exit 0
fi

# Get Private vs Public IP
function getIP {
  if [ "$DRIVER" == "amazonec2" ]; then
    echo $(docker-machine inspect -f '{{ .Driver.PrivateIPAddress }}' $1)
  else 
    echo $(docker-machine inspect -f '{{ .Driver.IPAddress }}' $1)
  fi
}

function check_status {
  if [ "$(docker-machine ls -f '{{ .Name }}' | grep ${MANAGER}1)" != "" ]; then
    error "${MANAGER}1 already exist. Please remove managerX and workerY machines"
  fi
}

# Create Docker host for managers
function create_manager {
  for i in $(seq 1 $NBR_MANAGER); do
    echo "-> creating Docker host for manager $i (please wait)"
    docker-machine create --driver $DRIVER $ADDITIONAL_PARAMS ${MANAGER}$i 1>/dev/null
  done
}

# Create Docker host for workers
function create_workers {
  for i in $(seq 1 $NBR_WORKER); do
    echo "-> creating Docker host for worker $i (please wait)"
    docker-machine create --driver $DRIVER $ADDITIONAL_PARAMS ${WORKER}$i 1>/dev/null
  done
}

# Init swarm from first manager
function init_swarm {
  echo "-> init swarm from ${MANAGER}1"
  docker-machine ssh ${MANAGER}1 $PERMISSION docker swarm init --listen-addr $(getIP ${MANAGER}1):2377
}

# Join other managers to the cluster
function join_other_managers {
  if [ "$((NBR_MANAGER-1))" -ge "1" ];then
    for i in $(seq 2 $NBR_MANAGER);do
      echo "-> ${MANAGER}$i requests membership to the swarm"
      docker-machine ssh ${MANAGER}$i $PERMISSION docker swarm join --manager --listen-addr $(getIP ${MANAGER}$i):2377 $(getIP ${MANAGER}1):2377 2>&1 
    done
  fi
}

# Accept other manager that requested membership to the cluster
function accept_other_managers {
  echo "-> accepting membership requests from other managers"
  for id in $(docker-machine ssh ${MANAGER}1 $PERMISSION docker node ls | grep Pending | awk '{print $1}'); do
    docker-machine ssh ${MANAGER}1 $PERMISSION docker node accept $id
  done
}

# Join worker to the cluster
function join_workers {
  for i in $(seq 1 $NBR_WORKER);do
    echo "-> join worker $i to the swarm"
    docker-machine ssh ${WORKER}$i $PERMISSION docker swarm join --listen-addr $(getIP ${WORKER}$i):2377 $(getIP ${MANAGER}1):2377
  done
}

# Deploy a test service
function deploy_service {
  echo "-> deploy service with $NBR_REPLICA replicas with exposed port $EXPOSED_PORT"
  SERVICE_ID=$(docker-machine ssh ${MANAGER}1 $PERMISSION docker service create --name demo --replicas $NBR_REPLICA --publish "${EXPOSED_PORT}:${SERVICE_PORT}" ${SERVICE_IMAGE})
  if [ "${SERVICE_ID}" == "" ]; then
    error "deploying service: no id returned"
  fi
}

# Wait for service to be available
function wait_service {
  echo "-> waiting for service ${SERVICE_ID} to be available"

  TASKS_NBR=$(docker-machine ssh ${MANAGER}1 $PERMISSION docker service ls | grep demo | awk '{print $3}' | cut -d '/' -f1)

  while [ "$TASKS_NBR" -lt "$NBR_REPLICA" ]; do
    echo "... retrying in 2 seconds"
    sleep 2
    TASKS_NBR=$(docker-machine ssh ${MANAGER}1 $PERMISSION docker service ls | grep demo | awk '{print $3}' | cut -d '/' -f1)
  done
}

# Display status
function status {
  echo "-> service available on port $EXPOSED_PORT of any node"

  echo "-> list available service"
  docker-machine ssh ${MANAGER}1 $PERMISSION docker service ls
  echo
  echo "-> list tasks"
  echo
  docker-machine ssh ${MANAGER}1 $PERMISSION docker service tasks demo
  echo 
  echo "-> list machines"
  docker-machine ls | egrep $PREFIX
  echo
  if [ "$DRIVER" == "amazonec2" ]; then
    echo "#####"
    echo "Warning: make sure you opened the port $EXPOSED_PORT in AWS security group used"
    echo "#####"
  fi
}

function main {
  check_status
  create_manager
  create_workers
  init_swarm
  join_other_managers
  accept_other_managers
  join_workers
  deploy_service
  wait_service
  status
}

main
