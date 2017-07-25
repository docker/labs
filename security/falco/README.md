# Lab: Sysdig Falco, Docker Security Auditing

> **Difficulty**: Medium

> **Time**: Approximately 50 minutes

Sysdig Falco is an open source, behavioral monitoring software designed to detect anomalous activity. Sysdig Falco works as a intrusion detection system on any Linux host, although it is particularly useful when using Docker since it supports container-specific context like **container.id**, **container.image** or **namespaces** for its rules.

Sysdig Falco is an *auditing* tool as opposed to *enforcement* tools like
[Seccomp](https://github.com/docker/labs/blob/master/security/seccomp/README.md) or [AppArmor](https://github.com/docker/labs/blob/master/security/apparmor/README.md). Falco runs in user space, using a kernel module to intercept system calls, while other similar tools perform system call filtering/monitoring at the kernel level. One of the benefits of a user space implementation is being able to integrate with external systems like Docker orchestration tools. [SELinux, Seccomp, Sysdig Falco, and you: A technical discussion](https://sysdig.com/blog/selinux-seccomp-falco-technical-discussion/) discusses the similarities and differences of these related security tools.

In this lab you will learn the basics of Sysdig Falco and how to use it along with Docker to detect anomalous container behavior.

You will simulate the following security threats as part of this lab:

- [Container running an interactive shell](#shell)
- [Unauthorized process](#process)
- [Unauthorized port open](#port)
- [Write to non user-data directory](#write)
- [Process attempts to read sensitive information after startup](#sensitive)
- [Sensitive mount by container](#mount)

You will play both the attacker and defender (sysadmin) roles, verifying that the intrusion attempt has
been detected by Sysdig Falco.

# Prerequisites

You will need all of the following to complete this lab:

- A Linux-based Docker Host.
- Some disposable containers to simulate the attacks.

To generate this lab `Ubuntu 16.04.2 LTS`, `Docker 17.06.0-ce`, and `Falco 0.7.0` were used. Any current version of the Linux kernel with the kernel headers available and a fairly modern Docker version should suffice.

# Falco installation and configuration

Sysdig Falco can be installed as a regular package from the repositories of popular distributions like Ubuntu or RHEL, there is also an [installation script](https://github.com/draios/falco/wiki/How-to-Install-Falco-for-Linux) that will auto detect your distro and install the required kernel headers.

For this lab, you will install using another method: a privileged Falco Docker container. It's cleaner for your Docker host node and easier to integrate into real production deployments.

As root, create a directory in your Docker host and download the configuration files that you will mount in the container

   ```
   # mkdir /etc/falco
   # cd /etc/falco
   # wget https://raw.githubusercontent.com/draios/falco/dev/rules/falco_rules.yaml
   # wget https://raw.githubusercontent.com/draios/falco/dev/falco.yaml
   # touch /var/log/falco_events.log
   ```
These are the two configuration files that you will need to modify in the examples below.
As you can guess, `falco.yaml` covers the daemon configuration, `falco_rules.yaml` contains the threat detection patterns and `falco_events.log` will be used as default output file.

The Falco container will build and load a kernel module using DKMS. Falco will run as a privileged container in order to build and load this kernel module, as well as have full visibility into the set of processes running on the local machine. This module is in charge of capturing Linux syscalls and other low-level events that will be exposed to the user-level process. Using this mechanism you don't need to modify or instrument the monitored containers in any way. Make sure that the appropriate kernel headers are installed and available under `lib/modules`.

Ubuntu/Debian instructions:

   ```
   # apt-get install linux-headers-$(uname -r)
   ```
Fedora/CentOS users will install the `kernel-devel-$(uname -r)` package and if you use any other distro, check how to install the kernel headers.

You can now pull and launch the Falco container

   ```
   docker pull sysdig/falco
   docker run -i -t --name falco --privileged -v /var/run/docker.sock:/host/var/run/docker.sock -v /dev:/host/dev -v /proc:/host/proc:ro -v /boot:/host/boot:ro -v /lib/modules:/host/lib/modules:ro -v /usr:/host/usr:ro -v /etc/falco/falco.yaml:/etc/falco.yaml -v /etc/falco/falco_rules.yaml:/etc/falco_rules.yaml -v /var/log/falco_events.log:/var/log/falco_events.log sysdig/falco
   ```

If you accidentally terminate the container or want to reload the configuration files, you can always `docker restart falco` from the host.

Check that the kernel module is correctly loaded

   ```
   # lsmod | grep falco

     falco_probe           442368  1
   ```

By default, Falco only logs to *syslog*, let's disable this option and enable file output, this way the exercises will be easier to follow.

Edit the `/etc/falco/falco.yaml` file and modify the `syslog_output` and `file_output` section:

   ```
   syslog_output:
     enabled: false

   file_output:
     enabled: true
     filename: /var/log/falco_events.log
   ```

Save the file and reload the container.

If you have not already, clone the lab and `cd` into the lab's `examplefiles` directory.

   ```
   $ git clone https://github.com/docker/labs.git
   $ cd labs/security/falco/examplefiles
   ```

There you will find the complete `falco.yaml` file and a (solution) `falco_rules.yaml` file.

# <a name="shell"></a> Container running an interactive shell

Let's start with an easy one, detecting an attacker running an interactive shell in any of your containers. This alert is included
in the default rule set. Let's trigger it first and then you can study the rule itself.

Run any container on your Docker host, for example `nginx`:

   ```
   # docker run -d -P --name example1 nginx

   # docker ps
   CONTAINER ID        IMAGE               COMMAND                  CREATED             STATUS              PORTS                   NAMES
   604aa46610dd        nginx               "nginx -g 'daemon ..."   2 minutes ago       Up 2 minutes        0.0.0.0:32771->80/tcp   example1
   ```

Now spawn an interactive shell

   ```
   # docker exec -it example1 bash
   ```

Tailing the `/var/log/falco_events.log` file you will be able to read:

   ```
   17:13:24.357351845: Notice A shell was spawned in a container with an attached terminal (user=root example1 (id=604aa46610dd) shell=bash parent=<NA> cmdline=bash  terminal=34816)
   ```

This is the specific `/etc/falco_rules.yaml` rule that fired

   ```
   - rule: Terminal shell in container
     desc: A shell was spawned by a program in a container with an attached terminal.
     condition: >
       spawned_process and container
       and shell_procs and proc.tty != 0
     output: "A shell was spawned in a container with an attached terminal (user=%user.name %container.info shell=%proc.name parent=%proc.pname cmdline=%proc.cmdline terminal=%proc.tty)"
     priority: NOTICE
     tags: [container, shell]
   ```

This is a rather complex rule, don't worry if you don't fully understand every section at this moment.

Notice that you can define and use macros to make your rules more readable and powerful. For example the `and container` condition above corresponds to the macro

   ```
   - macro: container
     condition: container.id != host
   ```

This is, any container id that doesn't match the hosting node (any actual container).

You can also classify different threat priorities [DEBUG, INFO, NOTICE, WARNING, ERROR...]

Note as well that the output message is much more useful including the context variables provided by Falco like `%proc.name` or `%container.info`.

But what if an untrusted process tries to directly spawn a shell inside our container namespace?  

There is a rule for that as well (search for `rule: Run shell untrusted` in the Falco rules file).

From the Docker host, let's spawn a shell directly in the `example1` container namespace

   ```
   #  docker inspect --format '{{.State.Pid}}' example1
   1768               # Your specific pid number will vary
   # nsenter --target 1768 --mount --uts --ipc --net --pid
   root@432436a38227:/# whoami
   root
   ```

Tailing the log, you can verify that this action has been detected by Falco

   ```
   13:01:26.610774404: Debug Shell spawned by untrusted binary (user=root shell=bash parent=nsenter cmdline=bash  pcmdline=nsenter --target 1768 --mount --uts --ipc --net --pid)
   ```

For the next exercise, you will create your own custom rule from scratch.

# <a name="process"></a> Unauthorized process

Docker and microservices design patterns recommend minimizing the number of processes per container. Apart from the architectural benefits, this
could be a huge advantage to security, because it completely restricts what should and should not be running on a particular container.

You know that your `nginx`containers should only be executing the `nginx` process (or a reduced set of processes in more complex scenarios). Anything else
should fire an alarm.

Let's write the following rule into `/etc/falco_rules.yaml`

   ```
   #Our nginx containers for example1 should only be running the 'nginx' process
   - rule: Unauthorized process on nginx containers
     desc: There is a process running in the nginx container that is not described in the template
     condition: spawned_process and container and container.image startswith nginx and not proc.name in (nginx)
     output: Unauthorized process (%proc.cmdline) running in (%container.id)
     priority: WARNING
   ```

You need to provide the `rule` name and `desc` entries for the human reader.
The firing condition requires:
 - `spawned_process` (default macro)
 - `container` (you don't want to fire this for the host)
 - `container.image startswith nginx` (so you can have separate authorized process lists for each container image)
 - `not proc.name in (nginx)` (you can write a comma separated list with the expected processes)

You already know how `output` and `priority` works.

Again, restart Falco and create the nginx container.

   ```
   # docker restart falco
   # docker run -d -P --name example2 nginx
   ```

spawn a shell in the `example2` container and just run anything like `ls`:

   ```
   # docker exec -it example2 ls
   ```

Tailing the `/var/log/falco_events.log` you will be able to read:

   ```
   18:38:43.364877988: Warning Unauthorized process (ls ) running in (604aa46610dd)
   ```

Success! The falco notification shows that Falco has recognized an unexpected process and is firing a warning.

You should probably comment out this rule before proceeding to the next exercises to get a cleaner output.

# <a name="port"></a> Unauthorized port

Similar to the previous exercise, if your container is opening a port that does not correlate to its service template, that's probably
something that should be checked.

this time, you can create a macro that contains your expected port numbers, so the rules you create later are shorter and easier to read:

   ```
   - macro: nginx_ports
     condition: fd.sport=80 or fd.sport=443 or fd.sport=8080

   ```

Now, write a rule that uses the macro

   ```
   - rule: Unauthorized port
     desc: Unauthorized port open on nginx container
     condition: inbound and container and container.image startswith nginx and not nginx_ports
     output: Unauthorized port (%fd.name) running in (%container.info)
     priority: WARNING
   ```

Let's reload Falco and create a disposable nginx container

   ```
   # docker restart falco
   # docker run -d -P --name example3 nginx
   ```

By default, the container exposes port 80, so you should receive no warning.

You can now spawn a shell into the container and install a text editor (remember to comment out the rule in example2 or this will generate a lot of noise).

   ```
   # docker exec -it example3 bash
   # apt update
   # apt install vim   # or your favorite text editor
   ```

Edit the nginx configuration file

   ```
   # vim /etc/nginx/conf.d/default.conf

   ```

you will see the directive `listen 80`, change it to a non authorized port, `listen 85` for example. Save the file and exit.

Reload the nginx service

   ```
   # service nginx reload
   ```

If you tail the `/var/log/falco_events.log` you will see two interesting entries:

   ```
   19:50:33.663139720: Error File below /etc opened for writing (user=root command=vim /etc/nginx/conf.d/default.conf file=/etc/nginx/conf.d/default.conf)
   19:50:51.031989661: Warning Unauthorized port (0.0.0.0:85) running in (example3 (id=6227a98c2d0b))
   ```

First one corresponds to a default Falco rule, usually you don't want a process to write in `/etc/`, second one is the custom rule you just created.

Comment out these macros and rules before moving on to the next section.

# <a name="write"></a> Write to non user-data directory

One of the key concepts using Docker is "immutability", usually, running containers are not supposed to be updated and the user data directories are
perfectly delimited. Let's use this design principle as a security indicator.

First, let's define a macro with the write-allowed directories:

   ```
   - macro: user_data_dir
     condition: evt.arg[1] startswith /userdata or evt.arg[1] startswith /var/log/nginx or evt.arg[1] startswith /var/run/nginx
   ```

You may want to include `/var/log/nginx` to avoid firing an alarm when nginx updates its logs.

And the rule for this exercise:

   ```
   - rule: Write to non user_data dir
     desc: attempt to write to directories that should be immutable
     condition: open_write and container and not user_data_dir
     output: "Writing to non user_data dir (user=%user.name command=%proc.cmdline file=%fd.name)"
     priority: ERROR

   ```

Let's take a look at the `open_write` macro:

   ```
   - macro: open_write
   condition: (evt.type=open or evt.type=openat) and evt.is_open_write=true and fd.typechar='f'
   ```

Just as a reminder that at its core, Falco performs a live capture of system calls like `open` or `openat`.

Now, you can spawn a new container and try this rule:

   ```
   # docker restart falco
   # docker run -d -P --name example4 nginx
   # docker exec -it example4 bash
   # mkdir /userdata
   # touch /userdata/foo   # Shouldn't trigger this rule
   # touch /usr/foo
   ```

Again, two relevant log lines:

   ```
   21:15:01.998703651: Error Writing to non user_data dir (user=root command=bash  file=/dev/tty)
   21:15:58.476945006: Error Writing to non user_data dir (user=root command=touch /usr/foo file=/usr/foo)
   ```

Your shell wrote to `/dev/tty`, and the non allowed file write to `/usr`.

# <a name="sensitive"></a> Process attempts to read sensitive information after startup

This is a rule already included in the default rule set, you will just adjust it to your use case.

This is the original rule

   ```
   - rule: Read sensitive file trusted after startup
     desc: an attempt to read any sensitive file (e.g. files containing user/password/authentication information) by a trusted program after startup. Trusted programs might read these files at startup to load initial state, but not afterwards.
     condition: sensitive_files and open_read and server_procs and not proc_is_new and proc.name!="sshd"
     output: "Sensitive file opened for reading by trusted program after startup (user=%user.name command=%proc.cmdline file=%fd.name)"
     priority: WARNING
    tags: [filesystem]
   ```

You haven't used the `tags` key before on your custom rules. Using tags you can
arbitrarily group sets of rules and run Falco with the `-T <tag>` to disable a set
of rules, or `-t <tag>` to *only* run the rules from the selected tag.

Let's focus on two of the macros from the former rule

`sensitive_files`

   ```
  - macro: sensitive_files
    condition: >
      fd.name startswith /etc and
      (fd.name in (/etc/shadow, /etc/sudoers, /etc/pam.conf)
       or fd.directory in (/etc/sudoers.d, /etc/pam.d))
   ```

These are the files or directories that you consider sensitive. You can add

   ```
   or fd.name startswith /dev
   ```

In case the malicious software / users tries to read from raw devices.

`server_procs`

   ```
   - macro: server_procs
    condition: proc.name in (http_server_binaries, db_server_binaries, docker_binaries, sshd)
   ```

These are the binaries considered safe that should always be allowed to read sensitive files and directories. Note that
you can include macros to define new macros.

You can now reload Falco and create a new disposable nginx container

   ```
   # docker restart falco
   # docker run -d -P --name example5 nginx
   # docker exec -it example5 bash
   # cat /etc/shadow
   ```

Checking the log, you can read the lines

   ```
   21:41:32.181638659: Warning Sensitive file opened for reading by non-trusted program (user=root name=cat command=cat /etc/shadow file=/etc/shadow)
   ```

# <a name="mount"></a> Sensitive mount by container

Docker containers usually have a strictly defined and static set of mountpoints, let's also use
this design limitation to your advantage. If a container tries to mount a host directory / file that
is outside the allowed directory set, that looks suspicious.   

You have this rule already in the default rules file:

   ```
   - rule: Launch Sensitive Mount Container
     desc: >
       Detect the initial process started by a container that has a mount from a sensitive host directory
       (i.e. /proc). Exceptions are made for known trusted images.
     condition: evt.type=execve and proc.vpid=1 and container and sensitive_mount and not trusted_containers
     output: Container with sensitive mount started (user=%user.name command=%proc.cmdline %container.info)
     priority: INFO
     tags: [container, cis]
   ```

Of course, you can modify the macro `sensitive_mount` to include the *forbidden* directories relevant to your case.

Launch an offending container

    ```
    # docker run -d -P --name example6 -v /proc:/tmp/proc nginx
    ```

And you will be able to read the log line

   ```
   13:32:41.070491862: Informational Container with sensitive mount started (user=root command=nginx -g daemon off; example6 (id=c46fa3bf0651))
   ```

# Using `falco-event-generator` to generate synthetic events

You may be wondering what other types of suspicious activity the default falco ruleset can detect. Falco has a synthetic event generator that shows off all the capibilities of the default ruleset. It can be run via a docker container:

   ```
   docker pull sysdig/falco-event-generator
   docker run -it --name falco-event-generator sysdig/falco-event-generator
   ```

If you run the event generator along with falco + the default falco ruleset, you'll see lots of suspicious activity detected:

```
19:00:55.362191761: Error File created below /dev by untrusted program (user=root command=event_generator  file=/dev/created-by-event-generator-sh)
19:00:56.365043165: Notice Database-related program spawned process other than itself (user=root program=sh -c ls > /dev/null parent=mysqld)
19:00:57.367928872: Warning Sensitive file opened for reading by non-trusted program (user=root name=event_generator command=event_generator  file=/etc/shadow)
19:00:59.370589147: Error File below known binary directory renamed/removed (user=root command=event_generator  operation=rename file=<NA> res=0 oldpath=/bin/true newpath=/bin/true.event-generator-sh )
19:00:59.370606844: Error File below known binary directory renamed/removed (user=root command=event_generator  operation=rename file=<NA> res=0 oldpath=/bin/true.event-generator-sh newpath=/bin/true )
19:01:00.371075563: Notice Unexpected setuid call by non-sudo, non-root program (user=bin parent=event_generator command=event_generator  uid=root)
19:01:01.372054445: Warning Sensitive file opened for reading by non-trusted program (user=root name=event_generator command=event_generator  file=/etc/shadow)
19:01:02.374407923: Warning Sensitive file opened for reading by non-trusted program (user=root name=httpd command=httpd --action read_sensitive_file --interval 6 --once file=/etc/shadow)
19:01:09.375758752: Notice A shell was spawned in a container with an attached terminal (user=root falco-event-generator (id=a4f221851741) shell=sh parent=event_generator cmdline=sh -c ls > /dev/null terminal=34830)
19:01:10.384443399: Notice Known system binary sent/received network traffic (user=root command=sha1sum --action network_activity --interval 0 --once connection=127.0.0.1:8192)
19:01:11.386501482: Informational System user ran an interactive command (user=bin command=login )
19:01:13.391107286: Error File below a known binary directory opened for writing (user=root command=event_generator  file=/bin/created-by-event-generator-sh)
19:01:14.391626865: Error File below /etc opened for writing (user=root command=event_generator  file=/etc/created-by-event-generator-sh)
19:01:15.392616291: Error Rpm database opened for writing by a non-rpm program (command=event_generator  file=/var/lib/rpm/created-by-event-generator-sh)
...
```

This can give you a good idea of the capabilities of falco.

# Conclusions & Further reading

In this lab you learned the basic of Sysdig Falco and its application in the Docker-based deployments.
Starting off from kernel system calls, events and Linux namespace context metadata, you can configure the relevant
alerts without ever having to modify or instrument the Docker images, preserving their immutable and encapsulated
design.

Just reading the default Falco rules file, you can find a lot of more advanced examples, like this one

   ```
   - rule: Change thread namespace
     desc: >
       an attempt to change a program/thread\'s namespace (commonly done
       as a part of creating a container) by calling setns.
     condition: >
       evt.type = setns
       and not proc.name in (docker_binaries, k8s_binaries, lxd_binaries, sysdigcloud_binaries, sysdig, nsenter)
       and not proc.name startswith "runc:"
       and not proc.pname in (sysdigcloud_binaries)
       and not java_running_sdjagent
     output: >
       Namespace change (setns) by unexpected program (user=%user.name command=%proc.cmdline
       parent=%proc.pname %container.info)
     priority: NOTICE
     tags: [process]
   ```

Which is particularly interesting since it is able to detect an unexpected `setns` system call, which can be used
to change the namespace of a running thread (and thus, to jailbreak docker process encapsulation).

You have used simple file output in order to focus on the rule syntax during this lab, but you can
also [configure a custom program output](https://github.com/draios/falco/wiki/Falco-Alerts#program-output)
to get proper notifications.

Further reading:

- [Sysdig Falco documentation](https://github.com/draios/falco/wiki)
- Blogpost [SELinux, Seccomp, Sysdig Falco, and you: A technical discussion](https://sysdig.com/blog/selinux-seccomp-falco-technical-discussion/)
- Demo video [Sysdig Falco - Man in the middle attack detection](https://www.youtube.com/watch?v=Hf8PxSJOMfw)
- [Public slack channel](https://slack.sysdig.com/), join channel #falco
