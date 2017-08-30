# Overlay networking and service discovery

# Lab Meta

> **Difficulty**: Intermediate

> **Time**: Approximately 20 minutes

In this lab you'll learn how to build, manage, and use an **overlay** network with a *service* in *Swarm mode*.

You will complete the following steps as part of this lab.

- [Step 1 - Create a new Swarm](#swarm_init)
- [Step 2 - Create an overlay network](#create_network)
- [Step 3 - Create a service](#create_service)
- [Step 4 - Test the network](#test)
- [Step 5 - Test service discovery](#discover)

# Prerequisites

You will need all of the following to complete this lab:

- Two Linux-based Docker hosts running **Docker 1.12** or higher in Engine mode (i.e. not yet configured for Swarm mode). You should use **node1** and **node2** from your lab.


# <a name="swarm_init"></a>Step 1: Create a new Swarm

In this step you'll initialize a new Swarm, join a single worker node, and verify the operations worked.

1. Execute the following command on **node1**.

    ```
    node1$ docker swarm init
    Swarm initialized: current node (cw6jpk7pqfg0jkilff5hr8z42) is now a manager.
    To add a worker to this swarm, run the following command:

    docker swarm join \
    --token SWMTKN-1-3n2iuzpj8jynx0zd8axr0ouoagvy0o75uk5aqjrn0297j4uaz7-63eslya31oza2ob78b88zg5xe \
    172.31.34.123:2377

    To add a manager to this swarm, run 'docker swarm join-token manager' and follow the instructions.
    ```

2. Copy the entire `docker swarm join` command that is displayed as part of the output from the command.

3. Paste the copied command into the terminal of **node2**.

    ```
    node2$ docker swarm join \
    >     --token SWMTKN-1-3n2iuzpj8jynx0zd8axr0ouoagvy0o75uk5aqjrn0297j4uaz7-63eslya31oza2ob78b88zg5xe \
    >     172.31.34.123:2377

    This node joined a swarm as a worker.
    ```

4. Run a `docker node ls` on **node1** to verify that both nodes are part of the Swarm.

    ```
    node1$ docker node ls
    ID                           HOSTNAME          STATUS  AVAILABILITY  MANAGER STATUS
    4nb02fhvhy8sb0ygcvwya9skr    ip-172-31-43-74   Ready   Active
    cw6jpk7pqfg0jkilff5hr8z42 *  ip-172-31-34-123  Ready   Active        Leader
    ```

    The `ID` and `HOSTNAME` values may be different in your lab. The important thing to check is that both nodes have joined the Swarm and are *ready* and *active*.

# <a name="create_network"></a>Step 2: Create an overlay network

Now that you have a Swarm initialized it's time to create an **overlay** network.

1. Create a new overlay network called "overnet" by executing the following command on **node1**.

    ```
    node1$ docker network create -d overlay overnet
    0cihm9yiolp0s9kcczchqorhb
    ```

2. Use the `docker network ls` command to verify the network was created successfully.

    ```
    node1$ docker network ls
    NETWORK ID          NAME                DRIVER      SCOPE
    1befe23acd58        bridge              bridge      local
    0ea6066635df        docker_gwbridge     bridge      local
    726ead8f4e6b        host                host        local
    8eqnahrmp9lv        ingress             overlay     swarm
    ef4896538cc7        none                null        local
    0cihm9yiolp0        overnet             overlay     swarm
    ```

    The new "overnet" network is shown on the last line of the output above. Notice how it is associated with the **overlay** driver and is scoped to the entire Swarm.

    > **NOTE:** The other new networks (ingress and docker_gwbridge) were created automatically when the Swarm cluster was created.

3. Run the same `docker network ls` command from **node2**

    ```
    node2$ docker network ls
    NETWORK ID          NAME                DRIVER      SCOPE
    b76635120433        bridge              bridge      local
    ea13f975a254        docker_gwbridge     bridge      local
    73edc8c0cc70        host                host        local
    8eqnahrmp9lv        ingress             overlay     swarm
    c4fb141606ca        none                null        local
    ```

    Notice that the "overnet" network does not appear in the list. This is because Docker only extends overlay networks to hosts when they are needed. This is usually when a host runs a task from a service that is created on the network. We will see this shortly.

4. Use the `docker network inspect` command to view more detailed information about the "overnet" network. You will need to run this command from **node1**.

    ```
    node1$ docker network inspect overnet
    [
        {
            "Name": "overnet",
            "Id": "0cihm9yiolp0s9kcczchqorhb",
            "Scope": "swarm",
            "Driver": "overlay",
            "EnableIPv6": false,
            "IPAM": {
                "Driver": "default",
                "Options": null,
                "Config": []
            },
            "Internal": false,
            "Containers": null,
            "Options": {
                "com.docker.network.driver.overlay.vxlanid_list": "257"
            },
            "Labels": null
        }
    ]
    ```

# <a name="create_service"></a>Step 3: Create a service

Now that you have a Swarm initialized and an overlay network, it's time to create a service that uses the network.

1. Execute the following command from **node1** to create a new service called *myservice* on the *overnet* network with two tasks/replicas.

    ```
    node1$ docker service create --name myservice \
    --network overnet \
    --replicas 2 \
    ubuntu sleep infinity

    e9xu03wsxhub3bij2tqyjey5t
    ```

2. Verify that the service is created and both replicas are up.

    ```
    node1$ docker service ls
    ID            NAME       REPLICAS  IMAGE   COMMAND
    e9xu03wsxhub  myservice  2/2       ubuntu  sleep infinity
    ```

    The `2/2` in the `REPLICAS` column shows that both tasks in the service are up and running.

3. Verify that a single task (replica) is running on each of the two nodes in the Swarm.

    ```
    node1$ docker service ps myservice
    ID            NAME         IMAGE   NODE   DESIRED STATE  CURRENT STATE  ERROR
    5t4wh...fsvz  myservice.1  ubuntu  node1  Running        Running 2 mins
    8d9b4...te27  myservice.2  ubuntu  node2  Running        Running 2 mins
    ```

    The `ID` and `NODE` values might be different in your output. The important thing to note is that each task/replica is running on a different node.

4. Now that **node2** is running a task on the "overnet" network it will be able to see the "overnet" network. Run the following command from **node2** to verify this.

    ```
    node2$ docker network ls
    NETWORK ID          NAME                DRIVER      SCOPE
    b76635120433        bridge              bridge      local
    ea13f975a254        docker_gwbridge     bridge      local
    73edc8c0cc70        host                host        local
    8eqnahrmp9lv        ingress             overlay     swarm
    c4fb141606ca        none                null        local
    0cihm9yiolp0        overnet             overlay     swarm
    ```

5. Run the following command on **node2** to get more detailed information about the "overnet" network and obtain the IP address of the task running on **node2**.

    ```
    node2$ docker network inspect overnet
    [
        {
            "Name": "overnet",
            "Id": "0cihm9yiolp0s9kcczchqorhb",
            "Scope": "swarm",
            "Driver": "overlay",
            "EnableIPv6": false,
            "IPAM": {
                "Driver": "default",
                "Options": null,
                "Config": [
                    {
                        "Subnet": "10.0.0.0/24",
                        "Gateway": "10.0.0.1"
                    }
                    ]
            },
            "Internal": false,
            "Containers": {
                "286d2e98c764...37f5870c868": {
                    "Name": "myservice.1.5t4wh7ngrzt9va3zlqxbmfsvz",
                    "EndpointID": "43590b5453a...4d641c0c913841d657",
                    "MacAddress": "02:42:0a:00:00:04",
                    "IPv4Address": "10.0.0.4/24",
                    "IPv6Address": ""
                }
            },      
            "Options": {
                "com.docker.network.driver.overlay.vxlanid_list": "257"
                },
                "Labels": {}
                }
            ]
    ```

You should note that as of Docker 1.12, `docker network inspect` only shows containers/tasks running on the local node. This means that `10.0.0.4` is the IPv4 address of the container running on **node2**. Make a note of this IP address for the next step (the IP address in your lab might be different than the one shown here in the lab guide).

# <a name="test"></a>Step 4: Test the network

To complete this step you will need the IP address of the service task running on **node2** that you saw in the previous step.

1. Execute the following commands from **node1**.

    ```
    node1$ docker network inspect overnet
    [
        {
            "Name": "overnet",
            "Id": "0cihm9yiolp0s9kcczchqorhb",
            "Scope": "swarm",
            "Driver": "overlay",
            "Containers": {
                "053abaa...e874f82d346c23a7a": {
                    "Name": "myservice.2.8d9b4i6vnm4hf6gdhxt40te27",
                    "EndpointID": "25d4d5...faf6abd60dba7ff9b5fff6",
                    "MacAddress": "02:42:0a:00:00:03",
                    "IPv4Address": "10.0.0.3/24",
                    "IPv6Address": ""
                }
            },      
            "Options": {
                "com.docker.network.driver.overlay.vxlanid_list": "257"
            },
            "Labels": {}
        }
    ]
    ```

    Notice that the IP address listed for the service task (container) running on **node1** is different to the IP address for the service task running on **node2**. Note also that they are one the sane "overnet" network.

2. Run a `docker ps` command to get the ID of the service task on **node1** so that you can log in to it in the next step.

    ```
    node1$ docker ps
    CONTAINER ID   IMAGE           COMMAND            CREATED      STATUS         NAMES
    053abaac4f93   ubuntu:latest   "sleep infinity"   19 mins ago  Up 19 mins     myservice.2.8d9b4i6vnm4hf6gdhxt40te27
    <Snip>
    ```

3. Log on to the service task. Be sure to use the container `ID` from your environment as it will be different from the example shown below.

    ```
    node1$ docker exec -it 053abaac4f93 /bin/bash
    root@053abaac4f93:/#
    ```

4. Install the ping command and ping the service task running on **node2**.

    ```
    root@053abaac4f93:/# apt-get update && apt-get install iputils-ping
    <Snip>
    root@053abaac4f93:/#
    root@053abaac4f93:/#
    root@053abaac4f93:/# ping 10.0.0.4
    PING 10.0.0.4 (10.0.0.4) 56(84) bytes of data.
    64 bytes from 10.0.0.4: icmp_seq=1 ttl=64 time=0.726 ms
    64 bytes from 10.0.0.4: icmp_seq=2 ttl=64 time=0.647 ms
    ^C
    --- 10.0.0.4 ping statistics ---
    2 packets transmitted, 2 received, 0% packet loss, time 999ms
    rtt min/avg/max/mdev = 0.647/0.686/0.726/0.047 ms
    ```

    The output above shows that both tasks from the **myservice** service are on the same overlay network spanning both nodes and that they can use this network to communicate.

# <a name="discover"></a>Step 5: Test service discovery

Now that you have a working service using an overlay network, let's test service discovery.

If you are not still inside of the container on **node1**, log back into it with the `docker exec` command.

1. Run the following command form inside of the container on **node1**.

    ```
    root@053abaac4f93:/# cat /etc/resolv.conf
    search eu-west-1.compute.internal
    nameserver 127.0.0.11
    options ndots:0
    ```

    The value that we are interested in is the `nameserver 127.0.0.11`. This value sends all DNS queries from the container to an embedded DNS resolver running inside the container listening on 127.0.0.11:53. All Docker container run an embedded DNS server at this address.

    > **NOTE:** Some of the other values in your file may be different to those shown in this guide.

2. Try and ping the `myservice` name from within the container.

    ```
    root@053abaac4f93:/# ping myservice
    PING myservice (10.0.0.2) 56(84) bytes of data.
    64 bytes from ip-10-0-0-2.eu-west-1.compute.internal (10.0.0.2): icmp_seq=1 ttl=64 time=0.020 ms
    64 bytes from ip-10-0-0-2.eu-west-1.compute.internal (10.0.0.2): icmp_seq=2 ttl=64 time=0.041 ms
    64 bytes from ip-10-0-0-2.eu-west-1.compute.internal (10.0.0.2): icmp_seq=3 ttl=64 time=0.039 ms
    ^C
    --- myservice ping statistics ---
    3 packets transmitted, 3 received, 0% packet loss, time 2001ms
    rtt min/avg/max/mdev = 0.020/0.033/0.041/0.010 ms
    ```

    The output clearly shows that the container can ping the `myservice` service by name. Notice that the IP address returned is `10.0.0.2`. In the next few steps we'll verify that this address is the virtual IP (VIP) assigned to the `myservice` service.

3. Type the `exit` command to leave the `exec` container session and return to the shell prompt of your **node1** Docker host.

4. Inspect the configuration of the `myservice` service and verify that the VIP value matches the value returned by the previous `ping myservice` command.

    ```
    node1$ docker service inspect myservice
    [
        {
            "ID": "e9xu03wsxhub3bij2tqyjey5t",
            "Version": {
                "Index": 20
            },
            "CreatedAt": "2016-11-23T09:28:57.888561605Z",
            "UpdatedAt": "2016-11-23T09:28:57.890326642Z",
            "Spec": {
                "Name": "myservice",
                "TaskTemplate": {
                    "ContainerSpec": {
                        "Image": "ubuntu",
                        "Args": [
                            "sleep",
                            "infinity"
                        ]
                    },
    <Snip>
            "Endpoint": {
                "Spec": {
                    "Mode": "vip"
                },
                "VirtualIPs": [
                    {
                        "NetworkID": "0cihm9yiolp0s9kcczchqorhb",
                        "Addr": "10.0.0.2/24"
                    }
    <Snip>
    ```

    Towards the bottom of the output you will see the VIP of the service listed. The VIP in the output above is `10.0.0.2` but the value may be different in your setup. The important point to note is that the VIP listed here matches the value returned by the `ping myservice` command.

Feel free to create a new `docker exec` session to the service task (container) running on **node2** and perform the same `ping service` command. You will get a response form the same VIP.
