# Docker daemon network options

When running the Docker daemon you don't often need to change options. If you do the [Docker daemon documentation][1] is the first place you should look for answers. Some of the network options are documented but may not be obvious when/where you would want to use them. This document will explain some of the options and give examples of when/where you would want to use them.

#### Daemon options

[`-b, --bridge`](#bridge)
[`--bip`](#bip)
[`--default-gateway`](#default-gateway)
[`--dns`](#dns)
[`--dns-opt`](#dns-opt)
[`--dns-search`](#dns-search)
[`--fixed-cidr`](#fixed-cider)
[`--ip`](#ip)
[`--ip-forward`](#ip-forward)
[`--ip-masq`](#ip-masq)
[`--iptables`](#iptables)
[`--ipv6`](#ipv6)
[`--mtu`](#mtu)
[`--userland-proxy`](#userland-proxy)

##### `bridge`
The bridge option will specify what bridge device the Docker daemon should use for containers. By default this will be docker0 but if you want to create a separate bridge interface you can specify it when you start the docker daemon with `--bridge`

##### `bip`
The bip will set the bridge IP network range. This is in [CIDR notation][3] so you can specify an alternate subnet for the docker bridge with `--bip=10.10.0.0/16` and containers will start with an address on the 10.10.0.0/16 network. In this example your first container will have the IP 10.10.0.2 (10.10.0.1 by default will be the gateway).

##### `default-gateway`

##### `dns`

##### `dns-opt`

##### `dns-search`

##### `fixed-cidr`

##### `ip`

##### `ip-forward`

##### `ip-masq`

##### `iptables`

##### `ipv6`

##### `mtu`

##### `userland-proxy`

[1]: https://docs.docker.com/v1.10/engine/reference/commandline/daemon/
[2]: https://docs.docker.com/v1.8/articles/networking/#bridge-building
[3]: https://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing
