# Swarm mode using Docker Machine

$managers=3
$workers=3

# Change the SwitchName to the name of your virtual switch
$SwitchName = "New Virtual Switch"

# create manager machines
echo "======> Creating manager machines ..."
for ($node=1;$node -le $managers;$node++) {
	echo "======> Creating manager$node machine ..."
	docker-machine create -d hyperv --hyperv-virtual-switch $SwitchName ('manager'+$node)
}

# create worker machines
echo "======> Creating worker machines ..."
for ($node=1;$node -le $workers;$node++) {
	echo "======> Creating worker$node machine ..."
	docker-machine create -d hyperv --hyperv-virtual-switch $SwitchName ('worker'+$node)
}

# list all machines
docker-machine ls
echo "======> Initializing first swarm manager ..."
$manager1ip = docker-machine ip manager1

docker-machine ssh manager1 "docker swarm init --listen-addr $manager1ip --advertise-addr $manager1ip"

# get manager and worker tokens
$managertoken = docker-machine ssh manager1 "docker swarm join-token manager -q"
$workertoken = docker-machine ssh manager1 "docker swarm join-token worker -q"

# other masters join swarm
for ($node=2;$node -le $managers;$node++) {
	echo "======> manager$node joining swarm as manager ..."
	$nodeip = docker-machine ip manager$node
	docker-machine ssh "manager$node" "docker swarm join --token $managertoken --listen-addr $nodeip --advertise-addr $nodeip $manager1ip"
}
# show members of swarm
docker-machine ssh manager1 "docker node ls"

# workers join swarm
for ($node=1;$node -le $workers;$node++) {
	echo "======> worker$node joining swarm as worker ..."
	$nodeip = docker-machine ip worker$node
	docker-machine ssh "worker$node" "docker swarm join --token $workertoken --listen-addr $nodeip --advertise-addr $nodeip $manager1ip"
}

# show members of swarm
docker-machine ssh manager1 "docker node ls"
