
## CNM Driver Interfaces
The Container Networking Model provides two pluggable and open interfaces that can be used by users, the community, and vendors to leverage additional functionality, visibility, or control in the network.

### Categories of Network Drivers

 - __Network Drivers__ — Docker Network Drivers provide the actual implementation that makes networks work. They are pluggable so that different drivers can be used and interchanged easily to support different use-cases. Multiple network drivers can be used on a given Docker Engine or Cluster concurrently, but each Docker network is only instantiated through a single network driver. There are two broad types of CNM network drivers:
 	- __Built-In Network Drivers__ — Built-In Network Drivers are a native part of the Docker Engine and are provided by Docker. There are multiple to choose from that support different capabilities like overlay networks or local bridges.
 	- __Plug-In Network Drivers__ — Plug-In Network Drivers are network drivers created by the community and other vendors. These drivers can be used to provide integration with incumbent software and hardware. Users can also create their own drivers in cases where they desire specific functionality that is not supported by an existing network driver.
 - __IPAM Drivers__ — Docker has a built-in IP Address Management Driver that provides default subnets or IP addresses for Networks and Endpoints if they are not specified. IP addressing can also be manually assigned through network, container, and service create commands. Plug-In IPAM drivers also exist that provide integration to existing IPAM tools. 

### Docker Built-In Network Drivers
The Docker built-in network drivers are part of Docker Engine and don't require any extra modules. They are invoked and used through standard `docker network` commands. The follow built-in network drivers exist:

- __Bridge__ — The `bridge` driver creates a Linux bridge on the host that is managed by Docker. By default containers on a bridge will be able to communicate with each other. External access to containers can also be configured through the `bridge` driver. 

- __Overlay__ — The `overlay` driver creates an overlay network that supports multi-host networks out of the box. It uses a combination of local Linux bridges and VXLAN to overlay container-to-container communications over physical network infrastructure. 

- __MACVLAN__ — The `macvlan` driver uses the MACVLAN bridge mode to establish a connection between container interfaces and a parent host interface (or sub-interfaces). It can be used to provide IP addresses to containers that are routable on the physical network. Additionally VLANs can be trunked to the `macvlan` driver to enforce Layer 2 container segmentation.

- __Host__ — With the `host` driver, a container uses the networking stack of the host. There is no namespace separation, and all interfaces on the host can be used directly by the container.

- __None__ — The `none` driver gives a container its own networking stack and network namespace but does not configure interfaces inside the container. Without additional configuration, the container is completely isolated from the host networking stack.


### Default Docker Networks
By default a `none`, `host`, and `bridge` network will exist on every Docker host. These networks cannot be removed. When instantiating a Swarm, two additional networks, a bridge network named `docker_gwbridge` and an overlay network named `ingress`, are automatically created to facilitate cluster networking. 

The `docker network ls` command shows these default Docker networks for a Docker Swarm:

```
NETWORK ID          NAME                DRIVER              SCOPE
1475f03fbecb        bridge              bridge              local
e2d8a4bd86cb        docker_gwbridge     bridge              local
407c477060e7        host                host                local
f4zr3zrswlyg        ingress             overlay             swarm
c97909a4b198        none                null                local
```

In addition to these default networks, [user defined networks](#userdefined) can also be created. They are discussed later in this document.

### Network Scope
As seen in the `docker network ls` output, Docker network drivers have a concept of _scope_. The network scope is the domain of the driver which can be the `local` or `swarm` scope. Local scope drivers provide connectivity and network services (such as DNS or IPAM) within the scope of the host. Swarm scope drivers provide connectivity and network services across a swarm cluster. Swarm scope networks will have the same network ID across the entire cluster while local scope networks will have a unique network ID on each host. 

### Docker Plug-In Network Drivers
The following community- and vendor-created plug-in network drivers are compatible with CNM. Each provides unique capabilities and network services for containers.

| Driver | Description   |
|------|------|
| [**contiv**](http://contiv.github.io/) | An open source network plugin led by Cisco Systems to provide infrastructure and security policies for multi-tenant microservices deployments. Contiv also provides integration for non-container workloads and with physical networks, such as ACI. Contiv implements plug-in network and IPAM drivers. |
| [**weave**](https://www.weave.works/docs/net/latest/introducing-weave/) |  A network plugin that creates a virtual network that connects Docker containers across multiple hosts or clouds. Weave provides automatic discovery of applications, can operate on partially connected networks, does not require an external cluster store, and is operations friendly.   |
| [**calico**](https://www.projectcalico.org/)     | Calico is an open source solution for virtual networking in cloud datacenters.  It targets datacenters where most of the workloads (VMs, containers, or bare metal servers) only require IP connectivity. Calico provides this connectivity using standard IP routing. Isolation between workloads — whether according to tenant ownership, or any finer grained policy — is achieved via iptables programming on the servers hosting the source and destination workloads.  |
| [**kuryr**](https://github.com/openstack/kuryr)    | A network plugin developed as part of the OpenStack Kuryr project. It implements the Docker networking (libnetwork) remote driver API by utilizing Neutron, the OpenStack networking service. Kuryr includes an IPAM driver as well. |

### Docker Plug-In IPAM Drivers
Community and vendor created IPAM drivers can also be used to provide integrations with existing systems or special capabilities.

| Driver | Description   |
|------|------|
| [**infoblox**](https://store.docker.com/community/images/infoblox/ipam-driver) | An open source IPAM plugin that provides integration with existing Infoblox tools. |

> There are many Docker plugins that exist and more are being created all the time. Docker maintains a list of the [most common plugins.](https://docs.docker.com/engine/extend/legacy_plugins/)

Next: **[Linux Network Fundamentals](03-linux-networking.md)**