# Networking Basics

There are many networking tutorials online that can explain the fundamentals of how networks work. This tutorial does not hope to explain all of networking but will focus on some of the elements that are in use when using Docker containers.

#### Default networking

Docker containers by default are run on a separate NAT'd network on the Docker host. What this means is your host will usually get an IP address from your DHCP server (e.g. 192.168.1.100) and your containers will be assigned an IP address from the Docker network they are connected to. By default this will be a bridge network created by the Docker daemon (docker0) with the network range 172.17.0.1/16. This means your first container will get the IP address 172.17.0.2; the second container will get 172.17.0.3 and so on. In the default bridge network, 172.17.0.1 is the default gateway which connects to the docker0 bridge on the host. This bridge allows network traffic from inside your container to get to the internet. The network traffic not destined for the 172.17.0.0/16 network will route through the default gateway of the container and then out of the default gateway of the Docker host (the router).

To better understand the topology in a picture you can see an example below.

<img src="https://raw.githubusercontent.com/rothgar/labs/network-tutorial/advanced/networking/images/docker-bridge.png" width="400" />

To see this in action inside a container you can run the following commands to view the IP address and route information.

```
$ sudo docker run --rm -it alpine sh
/ # ip address show dev eth0
21: eth0@if22: <BROADCAST,MULTICAST,UP,LOWER_UP,M-DOWN> mtu 1500 qdisc noqueue state UP 
    link/ether 02:42:ac:11:00:02 brd ff:ff:ff:ff:ff:ff
    inet 172.17.0.2/16 scope global eth0
       valid_lft forever preferred_lft forever
    inet6 fe80::42:acff:fe11:2/64 scope link 
       valid_lft forever preferred_lft forever
/ # ip route show
default via 172.17.0.1 dev eth0 
172.17.0.0/16 dev eth0  src 172.17.0.2
```

There is one other important thing the Docker daemon does by default in bridge mode, it sets an [`iptables`][1] POSTROUTING rule to MASQUERADE as the host. What that means is when traffic that originates from a container (from docker0 bridge) goes out through the host's eth0 interface it will pretend the traffic originated from the Docker host instead of from the container. This will allow the router to route return traffic back to the host. When the traffic returns to the host the iptables FORWARD chain will find the traffic destined to the docker0 network and route the packets the correct container.

You can view the the iptables rules on the docker host with (output clipped to relevant rules)

```
$ iptables -L -v
...
Chain FORWARD (policy ACCEPT 0 packets, 0 bytes)
 pkts bytes target     prot opt in     out     source               destination         
     0     0 DOCKER-ISOLATION  all  --  any    any     anywhere             anywhere            
         0     0 DOCKER     all  --  any    docker0  anywhere             anywhere
...
```

#### Alternate networking

All of the above is default behavior of docker container networking but it is not the only option. A default Docker installation will also make two other network options. A "host" and "none" network will also be created. You can specify what network a container should use at runtime with `--net=`. You can view the available docker networks with `docker network ls`.

```
$ docker network ls
NETWORK ID          NAME                DRIVER
1271b0977986        bridge              bridge              
50334a8f158c        host                host                
b91f7ed6218f        none                null
```
The "none" network will create a container with no outside network access, and "host" network will create a container in the same network namespace and with the same network devices as the host.

In addition to these default networks you can create your own networks and add functionallity with network plugins. You can read more about docker networking in [Docker network documentation][2].

[1]: https://en.wikipedia.org/wiki/Iptables
[2]: https://docs.docker.com/engine/userguide/networking/dockernetworks/
