# Lab: Capabilities

> **Difficulty**: Advanced

> **Time**: Approximately 30 minutes

In this lab you'll learn the basics of capabilities in the Linux kernel. You'll learn how they work with Docker, some basic commands to view and manage them, as well as how to add and remove capabilities in new containers.

You will complete the following steps as part of this lab.

- [Step 1 - Introduction to capabilities](#cap_intro)
- [Step 2 - Working with Docker and capabilities](#docker_cap)
- [Step 3 - Testing Docker capabilities](#test_docker)
- [Step 4 - Extra for experts](#extra)

# Prerequisites

You will need all of the following to complete this lab:

- A Linux-based Docker Host running Docker 1.13 or higher

# <a name="cap_intro"></a>Step 1: Introduction to capabilities

In this step you'll learn the basics of capabilities.

The Linux kernel is able to break down the privileges of the `root` user into distinct units referred to as **capabilities**. For example, the CAP_CHOWN capability is what allows the root use to make arbitrary changes to file UIDs and GIDs. The CAP_DAC_OVERRIDE capability allows the root user to bypass kernel permission checks on file read, write and execute operations. Almost all of the special powers associated with the Linux root user are broken down into individual capabilities.

This breaking down of root privileges into granular capabilities allows you to:

1. Remove individual capabilities from the `root` user account, making it less powerful/dangerous.
2. Add privileges to non-root users at a very granular level.

Capabilities apply to both files and threads. File capabilities allow users to execute programs with higher privileges. This is similar to the way the `setuid` bit works. Thread capabilities keep track of the current state of capabilities in running programs.

The Linux kernel lets you set capability *bounding sets* that impose limits on the capabilities that a file/thread can gain.

Docker imposes certain limitations that make working with capabilities much simpler. For example, file capabilities are stored within a file's extended attributes, and extended attributes are stripped out when Docker images are built. This means you will not normally have to concern yourself too much with file capabilities in containers.

> It is of course possible to get file capabilities into containers at runtime, however this is not recommended.

In an environment without file based capabilities, it's not possible for applications to escalate their privileges beyond the *bounding set* (a set beyond which capabilities cannot grow). Docker sets the *bounding set* before starting a container. You can use Docker commands to add or remove capabilities to or from the *bounding set*.

By default, Docker drops all capabilities except [those needed](https://github.com/moby/moby/blob/master/oci/defaults_linux.go#L64-L79), using a whitelist approach.

# <a name="docker_cap"></a>Step 2: Working with Docker and capabilities

In this step you'll learn the basic approach to managing capabilities with Docker. You'll also learn the Docker commands used to manage capabilities for a container's root account.

As of Docker 1.12 you have 3 high level options for using capabilities:

1. Run containers as root with a large set of capabilities and try to manage capabilities within your container manually.
2. Run containers as root with limited capabilities and never change them within a container.
3. Run containers as an unprivileged user with no capabilities.

Option 2 as the most realistic as of Docker 1.12. Option 3 would be ideal but not realistic. Option 1 should be avoided wherever possible.

> **Note:** Another option may be added in future versions of Docker that will allow you to run containers as a non-root user with added capabilities. The correct way of doing this requires *ambient capabilities* which was added to the Linux kernel in version 4.3. Whether it is possible for Docker to approximate this behavior in older kernels requires more research.

In the following commands, `$CAP` will be used to indicate one or more individual capabilities.

1. To drop capabilities from the `root` account of a container.

   ```
   $sudo docker run --rm -it --cap-drop $CAP alpine sh
   ```

2. To add capabilities to the `root` account of a container.

   ```
   $ sudo docker run --rm -it --cap-add $CAP alpine sh
   ```

3. To drop all capabilities and then explicitly add individual capabilities to the `root` account of a container.

   ```
   $ sudo docker run --rm -it --cap-drop ALL --cap-add $CAP alpine sh
   ```

The Linux kernel prefixes all capability constants with "CAP_". For example, CAP_CHOWN, CAP_NET_ADMIN, CAP_SETUID, CAP\_SYSADMIN etc. Docker capability constants are not prefixed with "CAP_" but otherwise match the kernel's constants.

For more information on capabilities, including a full list, see the [capabilities man page](http://man7.org/linux/man-pages/man7/capabilities.7.html)

# <a name="test_docker"></a>Step 3: Testing Docker capabilities

In this step you will start various new containers. Each time you will use the commands learned in the previous step to tweak the capabilities associated with the account used to run the container.

1. Start a new container and prove that the container's root account can change the ownership of files.

   ```
   $ docker container run --rm -it alpine chown nobody /
   ```

   The command gives no return code indicating that the operation succeeded. The command works because the default behavior is for new containers to be started with a root user. This root user has the CAP_CHOWN capability by default.

2. Start another new container and drop all capabilities for the containers root account other than the CAP\_CHOWN capability. Remember that Docker does not use the "CAP_" prefix when addressing capability constants.

   ```
   $ docker container run --rm -it --cap-drop ALL --cap-add CHOWN alpine chown nobody /
   ```

   This command also gives no return code, indicating a successful run. The operation succeeds because although you dropped all capabilities for the container's `root` account, you added the `chown` capability back. The `chown` capability is all that is needed to change the ownership of a file.

3. Start another new container and drop only the `CHOWN` capability form its root account.

   ```
   $ docker container run --rm -it --cap-drop CHOWN alpine chown nobody /
   chown: /: Operation not permitted
   ```

   This time the command returns an error code indicating it failed. This is because the container's root account does not have the `CHOWN` capability and therefore cannot change the ownership of a file or directory.

4. Create another new container and try adding the `CHOWN` capability to the non-root user called `nobody`. As part of the same command try and change the ownership of a file or folder.

   ```
   $ docker container run --rm -it --cap-add chown -u nobody alpine chown nobody /
   chown: /: Operation not permitted
   ```

   The above command fails because Docker does not yet support adding capabilities to non-root users.

In this step you have added and removed capabilities to a range of new containers. You have seen that capabilities can be added and removed from the root user of a container at a very granular level. You also learned that Docker does not currently support adding capabilities to non-root users.

# <a name="extra"></a>Step 4: Extra for experts

The remainder of this lab will show you additional tools for working with capabilities form the Linux shell.

There are two main sets of tools for managing capabilities:
- **libcap** focuses on manipulating capabilities.
- **libcap-ng** has some useful tools for auditing.

Below are some useful commands from both.

> You may need to manually install the packages required for some of these commands.`sudo apt-get install libcap-dev`, `sudo apt-get install libcap-ng-dev`, `sudo apt-get install libcap-ng-utils`.

## **libcap**

* `capsh` - lets you perform capability testing and limited debugging
* `setcap` - set capability bits on a file
* `getcap` - get the capability bits from a file

## **libcap-ng**

* `pscap` - list the capabilities of running processes
* `filecap` - list the capabilities of files
* `captest` - test capabilities as well as list capabilities for current process

The remainder of this step will show you some examples of `libcap` and `libcap-ng`.

### Listing all capabilities

The following command will start a new container using Alpine Linux, install the `libcap` package and then list capabilities.

   ```
   $ docker container run --rm -it alpine sh -c 'apk add -U libcap; capsh --print'

   (1/1) Installing libcap (2.25-r0)
   Executing busybox-1.24.2-r9.trigger
   OK: 5 MiB in 12 packages
   Current: = cap_chown,cap_dac_override,cap_fowner,cap_fsetid,cap_kill,cap_setgid,cap_setuid,cap_setpcap,cap_net_bind_service,cap_net_raw,cap_sys_chroot,cap_mknod,cap_audit_write,cap_setfcap+eip
   Bounding set =cap_chown,cap_dac_override,cap_fowner,cap_fsetid,cap_kill,cap_setgid,cap_setuid,cap_setpcap,cap_net_bind_service,cap_net_raw,cap_sys_chroot,cap_mknod,cap_audit_write,cap_setfcap
   Securebits: 00/0x0/1'b0
    secure-noroot: no (unlocked)
    secure-no-suid-fixup: no (unlocked)
    secure-keep-caps: no (unlocked)
   uid=0(root)
   gid=0(root)
   groups=0(root),1(bin),2(daemon),3(sys),4(adm),6(disk),10(wheel),11(floppy),20(dialout),26(tape),27(video)
   ```

In the output above, **Current** is multiple sets separated by spaces. Multiple capabilities within the same *set* are separated by commas `,`. The letters following the `+` at the end of each set are as follows:
- `e` is effective
- `i` is inheritable
- `p` is permitted

For information on what these mean, see the [capabilities manpage](http://man7.org/linux/man-pages/man7/capabilities.7.html).

### Experimenting with capabilities

The `capsh` command can be useful for experimenting with capabilities. `capsh --help` shows how to use the command:

```
$ sudo capsh --help
usage: capsh [args ...]
  --help         this message (or try 'man capsh')
  --print        display capability relevant state
  --decode=xxx   decode a hex string to a list of caps
  --supports=xxx exit 1 if capability xxx unsupported
  --drop=xxx     remove xxx,.. capabilities from bset
  --caps=xxx     set caps as per cap_from_text()
  --inh=xxx      set xxx,.. inheritiable set
  --secbits=<n>  write a new value for securebits
  --keep=<n>     set keep-capabability bit to <n>
  --uid=<n>      set uid to <n> (hint: id <username>)
  --gid=<n>      set gid to <n> (hint: id <username>)
  --groups=g,... set the supplemental groups
  --user=<name>  set uid,gid and groups to that of user
  --chroot=path  chroot(2) to this path
  --killit=<n>   send signal(n) to child
  --forkfor=<n>  fork and make child sleep for <n> sec
  ==             re-exec(capsh) with args as for --
  --             remaing arguments are for /bin/bash
                 (without -- [capsh] will simply exit(0))
```

> Warning:
> `--drop` sounds like what you want to do, but it only affects the bounding set. This can be very confusing because it doesn't actually take away the capability from the effective or inheritable set. You almost always want to use `--caps`, `sudo apt-get install attr`.

### Modifying capabilities

Libcap and libcap-ng can both be used to modify capabilities.

1. Use libcap to modify the capabilities on a file.

   The command below shows how to set the CAP_NET_RAW capability as *effective* and *permitted* on the file represented by `$file`. The `setcap` command calls on libcap to do this.

   ```
   $ sudo setcap cap_net_raw=ep $file
   ```

2. Use libcap-ng to set the capabilities of a file.

   The `filecap` command calls on libcap-ng.

   ```
   $ filecap /absolute/path net_raw
   ```

   **Note:** `filecap` requires absolute path names. Shortcuts like `./` are not permitted.

### Auditing

There are multiple ways to read out the capabilities from a file.

1. Using libcap:

   ```
   $ getcap $file

   $file = cap_net_raw+ep
   ```

2. Using libcap-ng:

   ```
   $ filecap /absolue/path/to/file

   file                     capabilities
   /absolute/path/to/file        net_raw
   ```

3. Using extended attributes (attr package):

   ```
   $ getfattr -n security.capability $file
   # file: $file
   security.capability=0sAQAAAgAgAAAAAAAAAAAAAAAAAAA=
   ```

### Tips

Docker images cannot have files with capability bits set. This reduces the risk of Docker containers using capabilities to escalate privileges. However, it is possible to mount volumes that contain files with capability bits set into containers. Therefore you should use caution if doing this.

1. You can audit directories for capability bits with the following commands:

```
# with libcap
$ getcap -r /

# with libcap-ng
$ filecap -a
```

2. To remove capability bits you can use.

```
# with libcap
$ setcap -r $file

# with libcap-ng
$ filecap /path/to/file none
```

### Further reading:

[This article](https://www.kernel.org/doc/ols/2008/ols2008v1-pages-163-172.pdf) explains capabilities in a lot of detail. It will help you understand how capability sets interact with each other, and is very useful if you plan to run privileged docker containers and manage capabilities manually inside of them.


[This is the man page for capabilities](http://man7.org/linux/man-pages/man7/capabilities.7.html). Most of the complex interactions between capability sets don't affect Docker containers as long as there are no files with capability bits set.
