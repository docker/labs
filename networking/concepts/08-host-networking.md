
## <a name="hostdriver"></a>Host (Native) Network Driver

The `host` network driver connects a container directly to the host networking stack. Containers using the `host` driver reside in the same network namespace as the host itself. Thus, containers will have native bare-metal network performance at the cost of namespace isolation. 

```bash
#Create a container attached to the host network namespace and print its network interfaces
$ docker run -it --net host --name c1 busybox ifconfig
docker0   Link encap:Ethernet  HWaddr 02:42:19:5F:BC:F7
          inet addr:172.17.0.1  Bcast:0.0.0.0  Mask:255.255.0.0
          UP BROADCAST MULTICAST  MTU:1500  Metric:1
          RX packets:0 errors:0 dropped:0 overruns:0 frame:0
          TX packets:0 errors:0 dropped:0 overruns:0 carrier:0
          collisions:0 txqueuelen:0
          RX bytes:0 (0.0 B)  TX bytes:0 (0.0 B)

eth0      Link encap:Ethernet  HWaddr 08:00:27:85:8E:95
          inet addr:10.0.2.15  Bcast:10.0.2.255  Mask:255.255.255.0
          inet6 addr: fe80::a00:27ff:fe85:8e95/64 Scope:Link
          UP BROADCAST RUNNING MULTICAST  MTU:1500  Metric:1
          RX packets:190780 errors:0 dropped:0 overruns:0 frame:0
          TX packets:58407 errors:0 dropped:0 overruns:0 carrier:0
          collisions:0 txqueuelen:1000
          RX bytes:189367384 (180.5 MiB)  TX bytes:3714724 (3.5 MiB)
...

#Display the interfaces on the host
$ ifconfig
docker0   Link encap:Ethernet  HWaddr 02:42:19:5f:bc:f7
          inet addr:172.17.0.1  Bcast:0.0.0.0  Mask:255.255.0.0
          UP BROADCAST MULTICAST  MTU:1500  Metric:1
          RX packets:0 errors:0 dropped:0 overruns:0 frame:0
          TX packets:0 errors:0 dropped:0 overruns:0 carrier:0
          collisions:0 txqueuelen:0
          RX bytes:0 (0.0 B)  TX bytes:0 (0.0 B)

eth0      Link encap:Ethernet  HWaddr 08:00:27:85:8e:95
          inet addr:10.0.2.15  Bcast:10.0.2.255  Mask:255.255.255.0
          inet6 addr: fe80::a00:27ff:fe85:8e95/64 Scope:Link
          UP BROADCAST RUNNING MULTICAST  MTU:1500  Metric:1
          RX packets:190812 errors:0 dropped:0 overruns:0 frame:0
          TX packets:58425 errors:0 dropped:0 overruns:0 carrier:0
          collisions:0 txqueuelen:1000
          RX bytes:189369886 (189.3 MB)  TX bytes:3716346 (3.7 MB)
...
```

In this example we can see that the host and container `c1` share the same interfaces. This has some interesting implications. Traffic passes directly from the container to the host interfaces.

With the `host` driver, Docker does not manage any portion of the container networking stack such as port mapping or routing rules. This means that common networking flags like `-p` and `--icc` have no meaning for the `host` driver. They will be ignored. If the network admin wishes to provide access and policy to containers then this will have to be self-managed on the host or managed by another tool.

Every container using the `host` network will all share the same host interfaces. This makes `host` ill suited for multi-tenant or highly secure applications. `host` containers will have access to every other container on the host. 

Full host access and no automated policy management may make the `host` driver a difficult fit as a general network driver. However, `host` does have some interesting properties that may be applicable for use cases such as ultra high performance applications, troubleshooting, or monitoring.

## <a name="nonedriver"></a>None (Isolated) Network Driver

Similar to the `host` network driver, the `none` network driver is essentially an unmanaged networking option. Docker Engine will not create interfaces inside the container, establish port mapping, or install routes for connectivity. A container using `--net=none` will be completely isolated from other containers and the host. The networking admin or external tools must be responsible for providing this plumbing. In the following example we see that a container using `none` only has a loopback interface and no other interfaces.


```bash
#Create a container using --net=none and display its interfaces 
$ docker run -it --net none busybox ifconfig
lo        Link encap:Local Loopback
          inet addr:127.0.0.1  Mask:255.0.0.0
          inet6 addr: ::1/128 Scope:Host
          UP LOOPBACK RUNNING  MTU:65536  Metric:1
          RX packets:0 errors:0 dropped:0 overruns:0 frame:0
          TX packets:0 errors:0 dropped:0 overruns:0 carrier:0
          collisions:0 txqueuelen:0
          RX bytes:0 (0.0 B)  TX bytes:0 (0.0 B)
```
 
Unlike the `host` driver, the `none` driver will create a separate namespace for each container. This guarantees container network isolation between any containers and the host. 

 > Containers using `--net=none` or `--net=host` cannot be connected to any other Docker networks.

 Next: **[Physical Network Design Requirements](09-physical-networking.md)**
