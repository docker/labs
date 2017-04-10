# Docker Networking Security Basics

# Lab Meta

> **Difficulty**: Beginner

> **Time**: Approximately 10 minutes

In this lab you'll look some of the built-in network security technologies available in Swarm Mode.

You will complete the following steps as part of this lab.

- [Step 1 - Create an encrypted overlay network](#network_create)
- [Step 2 - List networks](#list_networks)
- [Step 3 - Deploy a service](#deploy_service)
- [Step 4 - Clean-up](#clean)

# Prerequisites

You will need all of the following to complete this lab:

- At least two Linux-based Docker Hosts running Docker 1.13 or higher and configured as part of the same Swarm
- This lab assumes Swarm with at least one manager nodes and one worker node. In this lab, **node1** will be the manager and **node3** will be the worker. You may need to change these values if your lab is configured differently - the important thing is that one node is a manager and the other is a worker.
- This lab was built and tested using Ubuntu 16.04 and Docker 17.03.0-ce

# <a name="network_create"></a>Step 1: Create an encrypted overlay network

In this step you will create two overlay networks. The first will only have the control plane traffic encrypted. The second will have control plane **and** data plane traffic encrypted.

All Docker overlay networks have control plane traffic encrypted by default. To encrypt data plane traffic you need to pass the `--opt encrypted` flag to the `docker network create command`.

Perform all of the following commands from a *Manager node* in your lab. The examples in this lab guide will assume you are using **node1**. Your lab may be different.

1. Create a new overlay network called **net1**

   ```
   $ docker network create -d overlay net1
   xt3jwgsq20ob648uc5f8ow95q
   ```

2. Inspect the **net1** network to check for the **encrypted** flag

   ```
   $ docker network inspect net1
   [
    {
        "Name": "net1",
        "Id": "xt3jwgsq20ob648uc5f8ow95q",
        "Created": "0001-01-01T00:00:00Z",
        "Scope": "swarm",
        "Driver": "overlay",
        "EnableIPv6": false,
        "IPAM": {
            "Driver": "default",
            "Options": null,
            "Config": []
        },
        "Internal": false,
        "Attachable": false,
        "Containers": null,
        "Options": {
            "com.docker.network.driver.overlay.vxlanid_list": "4097"
        },
        "Labels": null
    }
   ]
   ```
   Notice that there is no **encrypted** flag under the **Options** section of the output. This indicates that data plane traffic (application traffic) is not encrypted on this network. Control plane traffic (gossip etc) is encrypted by default for all overlay networks.

3. Create another overlay network, but this time pass the `--opt encrypted` flag. Call this network **net2**.

   ```
   $ docker network create -d overlay --opt encrypted net2
   uaaw8ljwidoc5is2qo362hd8n
   ```

4. Inspect the **net2** network to check for the **encrypted** flag

   ```
   $ docker network inspect net2
   [
    {
        "Name": "net2",
        "Id": "uaaw8ljwidoc5is2qo362hd8n",
        "Created": "0001-01-01T00:00:00Z",
        "Scope": "swarm",
        "Driver": "overlay",
        "EnableIPv6": false,
        "IPAM": {
            "Driver": "default",
            "Options": null,
            "Config": []
        },
        "Internal": false,
        "Attachable": false,
        "Containers": null,
        "Options": {
            "com.docker.network.driver.overlay.vxlanid_list": "4098",
            "encrypted": ""
        },
        "Labels": null
    }
   ]
   ```

   Notice the presence of the **encrypted** flag below the VXLAN ID in the **Options** field. This indicates that data plane traffic (application traffic) on this network will be encrypted.


# <a name="list_networks"></a>Step 2: List networks

In this step you will list the networks visible on **node1** (*manager node*) and **node3** (*worker node*) in your lab.  The networks you created in the previous step will be visible on **node1** but not **node3**. This is because Docker takes a lazy approach when propagating networks to *worker nodes* - a *worker node* only gets to know about a network if it runs a container or service task that specifically requires that network. This reduces network control plane chatter which assists with scalability and security.

>NOTE: All *manager nodes* know about all networks.

1. Run the `docker network ls` command on **node1**

   ```
   node1$ docker network ls
   NETWORK ID          NAME                DRIVER              SCOPE
   70bd606f9f81        bridge              bridge              local
   475a3b8f04de        docker_gwbridge     bridge              local
   f94f673bfe7e        host                host                local
   3ecc06xxyb7d        ingress             overlay             swarm
   xt3jwgsq20ob        net1                overlay             swarm
   uaaw8ljwidoc        net2                overlay             swarm
   b535831c780f        none                null                local
   ```

   Notice that **net1** and **net2** are both present in the list. This is expected behavior because you created these networks on **node1** and it is also a *manager node*. *Worker nodes* in the Swarm should not be able to see these networks yet.

2. Run the `docker network ls` command on **node3** (*worker node*)

   ```
   node3$ docker network ls
   NETWORK ID          NAME                DRIVER              SCOPE
   abe97d2963b3        bridge              bridge              local
   42295053cd72        docker_gwbridge     bridge              local
   ad4f60192aa0        host                host                local
   3ecc06xxyb7d        ingress             overlay             swarm
   1a85d1a0721f        none                null                local
   ```

   The **net1** and **net2** networks are not visible on this *worker node*. This is expected behavior because the node is not running a service task that is on that network. This proves that Docker does not extend newly created networks to all *worker nodes* in a Swarm - it delays this action until a node has a specific requirement to know about that network. This improves scalability and security.

# <a name="deploy_service"></a>Step 3: Deploy a service

In this step you will deploy a service on the **net2** overlay network. You will deploy the service with enough replica tasks so that at least one task will run on every node in your Swarm. This will force Docker to extend the **net2** network to all nodes in the Swarm.

1. Deploy a new service to all nodes in your Swarm. When executing this command, be sure to use an adequate number of replica tasks so that all Swarm nodes will run a task. This example deploys 4 replica tasks.

   ```
   $ docker service create --name service1 \
   --network=net2 --replicas=4 \
   alpine:latest sleep 1d

   ivfei61h3jvypuj7v0443ow84
   ```
2. Check that the service has deployed successfully

   ```
   $ docker service ls
   ID            NAME      MODE        REPLICAS  IMAGE
   ivfei61h3jvy  service1  replicated  4/4       alpine:latest
   ```

   As long as all replicas are up (`4/4` in the example above) you can proceed to the next command. It may take a minute for the service tasks to deploy while the image is downloaded to each node in your Swarm.

3. Run the `docker network ls` command again from **node3**.

   >NOTE: It is important that you run this step from a *worker node* that could previously not see the **net2** network.

   ```
   node3$ docker network ls
   NETWORK ID          NAME                DRIVER              SCOPE
   abe97d2963b3        bridge              bridge              local
   42295053cd72        docker_gwbridge     bridge              local
   ad4f60192aa0        host                host                local
   3ecc06xxyb7d        ingress             overlay             swarm
   uaaw8ljwidoc        net2                overlay             swarm
   1a85d1a0721f        none                null                local
   ```

   The **net2** network is now visible on **node3**. This is because **node3** is running a task for the **service1** service which is using the **net2** network.

Congratulations! You've created an encrypted network, deployed a service to it, and seen that new overlay networks are only made available to worker nodes in the Swarm as and when they runs service tasks on the network.

# <a name="clean"></a>Step 4: Clean-up

In this step you will clean-up the service and networks created in this lab.

Execute all of the following commands from **node1** or another Swarm manager node.

1. Remove the service you created in Step 3

   ```
   $ docker service rm service1
   service1
   ```
   This will also remove the **net2** network from all worker nodes in the Swarm.

2. Remove the **net1** and **net2** networks

   ```
   $ docker network rm net1 net2
   net1
   net2
   ```
Congratulations. You've completed this quick Docker Network Security lab. You've even cleaned up!
