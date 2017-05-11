## <a name="ipam"></a>IP Address Management

The Container Networking Model (CNM) provides flexibility in how IP addresses are managed. There are two methods for IP address management.

- CNM has a built-in IPAM driver that does simple allocation of IP addresses globally for a cluster and prevents overlapping allocations. The built-in IPAM driver is what is used by default if no other driver is specified. 
- CNM has interfaces to use plug-in IPAM drivers from other vendors and the community. These drivers can provide integration into existing vendor or self-built IPAM tools. 

Manual configuration of container IP addresses and network subnets can be done using UCP, the CLI, or Docker APIs. The address request will go through the chosen driver which will decide how to process the request.

Subnet size and design is largely dependent on a given application and the specific network driver. IP address space design is covered in more depth for each [Network Deployment Model](#models) in the next section. The uses of port mapping, overlays, and MACVLAN all have implications on how IP addressing is arranged. In general, container addressing falls into two buckets. Internal container networks (bridge and overlay) address containers with IP addresses that are not routable on the physical network by default. MACVLAN networks provide IP addresses to containers that are on the subnet of the physical network. Thus, traffic from container interfaces can be routable on the physical network. It is important to note that subnets for internal networks (bridge, overlay) should not conflict with the IP space of the physical underlay network. Overlapping address space can cause traffic to not reach its destination. 

Next: **[Network Troubleshooting](13-troubleshooting.md)**
