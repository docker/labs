# Lab: Seccomp

> **Difficulty**: Advanced

> **Time**: Approximately 20 minutes

seccomp is a sandboxing facility in the Linux kernel that acts like a firewall for system calls (syscalls). It uses Berkeley Packet Filter (BPF) rules to filter syscalls and control how they are handled. These filters can significantly limit a containers access to the Docker Host's Linux kernel - especially for simple containers/applications.

You will complete the following steps as part of this lab.

- [Step 1 - Clone the labs GitHub repo](#clone)
- [Step 2 - Test a seccomp profile](#test)
- [Step 3 - Run a container with no seccomp profile](#no-default)
- [Step 4 - Selectively remove syscalls](#chmod)
- [Step 5 - Write a seccomp profile](#write)
- [Step 6 - A few Gotchas](#gotchas)

# Prerequisites

You will need all of the following to complete this lab:

- A Linux-based Docker Host with seccomp enabled
- Docker 1.10 or higher (preferably 1.12 or higher)
- This lab was created using Ubuntu 16.04 and Docker 17.04.0-ce. If you are using older versions of Docker you may need to replace `docker container run` commands with `docker run` commands.

The following commands show you how to check if seccomp is enabled in your system's kernel:

   Check from Docker 1.12 or higher
   ```
   $ docker info | grep seccomp
   Security Options: apparmor seccomp
   ```
   If the above output does not return a line with `seccomp` then your system does not have seccomp enabled in its kernel.

   Check from the Linux command line
   ```
   $ grep SECCOMP /boot/config-$(uname -r)
   CONFIG_HAVE_ARCH_SECCOMP_FILTER=y
   CONFIG_SECCOMP_FILTER=y
   CONFIG_SECCOMP=y
   ```

### Seccomp and Docker

Docker has used seccomp since version 1.10 of the Docker Engine.

Docker uses seccomp in *filter mode* and has its own JSON-based DSL that allows you to define *profiles* that compile down to seccomp filters. When you run a container it gets the default seccomp profile unless you override this by passing the `--security-opt` flag to the `docker run` command.

The following example command starts an interactive container based off the Alpine image and starts a shell process. It also applies the seccomp profile described by `<profile>.json` to it.

   ```
   $ docker container run -it --rm --security-opt seccomp=<profile>.json alpine sh ...
   ```

The above command sends the JSON file from the client to the daemon where it is compiled into a BPF program using a [thin Go wrapper around libseccomp](https://github.com/seccomp/libseccomp-golang).

Docker seccomp profiles operate using a whitelist approach that specifies allowed syscalls. *Only* syscalls on the whitelist are permitted.

Docker supports many security related technologies. It is possible for other security related technologies to interfere with your testing of seccomp profiles. For this reason, the best way to test the effect of seccomp profiles is to add all *capabilities* and disable *apparmor*. This gives you the confidence the behavior you see in the following steps is solely due to seccomp changes.

The following `docker run` flags add all *capabilities* and disable *apparmor*: `--cap-add ALL --security-opt apparmor=unconfined`.

# <a name="clone"></a>Step 1: Clone the labs GitHub repo

In this step you will clone the lab's GitHub repo so that you have the seccomp profiles that you will use for the remainder of this lab.

1. Clone the labs GitHub repo.

   ```
   $ git clone https://github.com/docker/labs
   ```

2. Change into the `labs/security/seccomp` directory.

   ```
   $ cd labs/security/seccomp/seccomp-profiles
   ```

The remaining steps in this lab will assume that you are running commands from this `labs/security/seccomp` directory. This will be important when referencing the seccomp profiles on the various `docker run` commands throughout the lab.

# <a name="test"></a>Step 2: Test a seccomp profile

In this step you will use the `deny.json` seccomp profile included the lab guides repo. This profile has an empty syscall whitelist meaning all syscalls will be blocked. As part of the demo you will add all *capabilities* and effectively disable *apparmor* so that you know that only your seccomp profile is preventing the syscalls.

1. Use the `docker run` command to try to start a new container with all capabilities added, apparmor unconfined, and the `seccomp-profiles/deny.json` seccomp profile applied.

   ```
   $ docker container run --rm -it --cap-add ALL --security-opt apparmor=unconfined --security-opt seccomp=seccomp-profiles/deny.json alpine sh
   docker: Error response from daemon: exit status 1: "cannot start a container that has run and stopped\n".
   ```

In this scenario, Docker doesn't actually have enough syscalls to start the container!

2. Inspect the contents of the `seccomp-profiles/deny.json` profile.

   ```
   $ cat seccomp-profiles/deny.json
   {
        "defaultAction": "SCMP_ACT_ERRNO",
        "architectures": [
                "SCMP_ARCH_X86_64",
                "SCMP_ARCH_X86",
                "SCMP_ARCH_X32"
        ],
        "syscalls": [
        ]
   }
   ```

   Notice that there are no **syscalls** in the whitelist. This means that no syscalls will be allowed from containers started with this profile.

In this step you removed *capabilities* and *apparmor* from interfering, and started a new container with a seccomp profile that had no syscalls in its whitelist. You saw how this prevented all syscalls from within the container or to let it start in the first place.

# <a name="no-default"></a>Step 3: Run a container with no seccomp profile

Unless you specify a different profile, Docker will apply the [default seccomp profile](https://github.com/moby/moby/blob/master/profiles/seccomp/default.json) to all new containers. In this step you will see how to force a new container to run without a seccomp profile.

1. Start a new container with the `--security-opt seccomp=unconfined` flag so that no seccomp profile is applied to it.

   ```
   $ docker container run --rm -it --security-opt seccomp=unconfined debian:jessie sh
   ```

2. From the terminal of the container run a `whoami` command to confirm that the container works and can make syscalls back to the Docker Host.

   ```
   / # whoami
   root
   ```

3. To prove that we are not running with the default seccomp profile, try running a `unshare` command, which creates a new namespace:
  ```
  / # unshare --map-root-user --user
  / # whoami
  root
  ```
   If you try running the above `unshare` command from a container with the default seccomp profile applied it will fail with an `Operation not permitted` error.

4. Exit the container.

5. Run the following `strace` command from your Docker Host to see a list of the syscalls used by the `whoami` program.

   Your Docker Host will need the `strace` package installed.

   ```
   $ strace -c -f -S name whoami 2>&1 1>/dev/null | tail -n +3 | head -n -2 | awk '{print $(NF)}'
   access
   arch_prctl
   brk
   close
   connect
   execve
   <SNIP>
   socket
   write
   ```

  You can also run the following simpler command and get a more verbose output.

   ```
   $ strace whoami
   execve("/usr/bin/whoami", ["whoami", "-qq"], [/* 21 vars */]) = 0
   brk(0)                                  = 0x1980000
   <SNIP>
   ```

   You can substitute **whoami** for any other program.

In this step you started a new container with no seccomp profile and verified that the `whoami` program could execute. You also used the `strace` program to list the syscalls made by a particular run of the `whoami` program.

# <a name="chmod"></a>Step 4: Selectively remove syscalls

In this step you will see how applying changes to the `default.json` profile can be a good way to fine-tune which syscalls are available to containers.

The `default-no-chmod.json` profile is a modification of the `default.json` profile with the `chmod()`, `fchmod()`, and `chmodat()` syscalls removed from its whitelist.

1. Start a new container with the `default-no-chmod.json` profile and attempt to run the `chmod 777 / -v` command.

   ```
   $ docker container run --rm -it --security-opt seccomp=default-no-chmod.json alpine sh

   / # chmod 777 / -v
   chmod: /: Operation not permitted
   ```

  The command fails because the `chmod 777 / -v` command uses some of the `chmod()`, `fchmod()`, and `chmodat()` syscalls that have been removed from the whitelist of the `default-no-chmod.json` profile.

2. Exit the container.

3. Start another new container with the `default.json` profile and run the same `chmod 777 / -v`.

   ```
   $ docker container run --rm -it --security-opt seccomp=default.json alpine sh

   / # chmod 777 / -v
   mode of '/' changed to 0777 (rwxrwxrwx)
   ```

  The command succeeds this time because the `default.json` profile has the `chmod()`, `fchmod()`, and `chmodat` syscalls included in its whitelist.

4. Exit the container.

5. Check both profiles for the presence of the `chmod()`, `fchmod()`, and `chmodat()` syscalls.

   Be sure to perform these commands from the command line of you Docker Host and not from inside of the container created in the previous step.

   ```
   $ cat ./seccomp-profiles/default.json | grep chmod
   "name": "chmod",
   "name": "fchmod",
   "name": "fchmodat",

   $ cat ./deccomp-profiles/default-no-chmod.json | grep chmod
   ```

   The output above shows that the `default-no-chmod.json` profile contains no **chmod** related syscalls in the whitelist.

In this step you saw how removing particular syscalls from the `default.json` profile can be a powerful way to start fine tuning the security of your containers.

# <a name="write"></a>Step 5: Write a seccomp profile

It is possible to write Docker seccomp profiles from scratch. You can also edit existing profiles. In this step you will learn about the syntax and behavior of Docker seccomp profiles.

The layout of a Docker seccomp profile looks like the following:

```
{
    "defaultAction": "SCMP_ACT_ERRNO",
    "architectures": [
        "SCMP_ARCH_X86_64",
        "SCMP_ARCH_X86",
        "SCMP_ARCH_X32"
    ],
    "syscalls": [
        {
            "name": "accept",
            "action": "SCMP_ACT_ALLOW",
            "args": []
        },
        {
            "name": "accept4",
            "action": "SCMP_ACT_ALLOW",
            "args": []
        },
        ...
    ]
}
```

The most authoritative source for how to write Docker seccomp profiles is the structs used to deserialize the JSON.

* https://github.com/docker/engine-api/blob/c15549e10366236b069e50ef26562fb24f5911d4/types/seccomp.go
* https://github.com/opencontainers/runtime-spec/blob/master/specs-go/config.go#L357

The table below lists the possible *actions* in order of precedence. Higher actions overrule lower actions.

| Action         | Description                                                              |
|----------------|--------------------------------------------------------------------------|
| SCMP_ACT_KILL  | Kill with a exit status of `0x80 + 31 (SIGSYS) = 159`                    |
| SCMP_ACT_TRAP  | Send a `SIGSYS` signal without executing the system call                 |
| SCMP_ACT_ERRNO | Set `errno` without executing the system call                            |
| SCMP_ACT_TRACE | Invoke a ptracer to make a decision or set `errno` to `-ENOSYS`          |
| SCMP_ACT_ALLOW | Allow                                                                    |

The most important actions for Docker users are `SCMP_ACT_ERRNO` and `SCMP_ACT_ALLOW`.

Profiles can contain more granular filters based on the value of the arguments to the system call.

```
{
    ...
    "syscalls": [
        {
            "name": "accept",
            "action": "SCMP_ACT_ALLOW",
            "args": [
                {
                    "index": 0,
                    "op": "SCMP_CMP_MASKED_EQ",
                    "value": 2080505856,
                    "valueTwo": 0
                }
            ]
        }
    ]
}
```

* `index` is the index of the system call argument
* `op` is the operation to perform on the argument. It can be one of:
    * SCMP_CMP_NE - not equal
    * SCMP_CMP_LT - less than
    * SCMP_CMP_LE - less than or equal to
    * SCMP_CMP_EQ - equal to
    * SCMP_CMP_GE - greater than
    * SCMP_CMP_GT - greater or equal to
    * SCMP_CMP_MASKED_EQ - masked equal: true if `(value & arg == valueTwo)`
* `value` is a parameter for the operation
* `valueTwo` is used only for SCMP_CMP_MASKED_EQ

The rule only matches if **all** args match. Add multiple rules to achieve the effect of an OR.

`strace` can be used to get a list of all system calls made by a program.
It's a very good starting point for writing seccomp policies.
Here's an example of how we can list all system calls made by `ls`:

```
$ strace -c -f -S name ls 2>&1 1>/dev/null | tail -n +3 | head -n -2 | awk '{print $(NF)}'
access
arch_prctl
brk
close
execve
<SNIP>
statfs
write
```

The output above shows the syscalls that will need to be enabled for a container running the `ls` program to work, in addition to the syscalls required to start a container.

In this step you learned the format and syntax of Docker seccomp profiles. You also learned the order of preference for actions, as well as how to determine the syscalls needed by an individual program.

# <a name="test"></a>Step 6: A few gotchas

The remainder of this lab will walk you through a few things that are easy to miss when using seccomp with Docker.

#### Timing of a seccomp profile application

In versions of Docker prior to 1.12, seccomp polices tended to be applied very early in the container creation process. This resulted in you needing to add syscalls to your profile that were required for the container creation process but not required by your container. This was not ideal. See:

- https://github.com/moby/moby/issues/22252
- https://github.com/opencontainers/runc/pull/789

A good way to avoid this issue in Docker 1.12+ can be to use the `--security-opt no-new-privileges` flag when starting your container. However, this will also prevent you from gaining privileges through `setuid` binaries.

#### Truncation

When writing a seccomp filter, there may be unused or randomly set bits on 32-bit arguments when using a 64-bit operating system after the filter has run.

> When checking values from args against a blacklist, keep in mind that
> arguments are often silently truncated before being processed, but
> after the seccomp check.  For example, this happens if the i386 ABI
> is used on an x86-64 kernel: although the kernel will normally not
> look beyond the 32 lowest bits of the arguments, the values of the
> full 64-bit registers will be present in the seccomp data.  A less
> surprising example is that if the x86-64 ABI is used to perform a
> system call that takes an argument of type int, the more-significant
> half of the argument register is ignored by the system call, but
> visible in the seccomp data.

https://www.kernel.org/doc/Documentation/prctl/seccomp_filter.txt

#### seccomp escapes

Syscall numbers are architecture dependent. This limits the portability of BPF filters. Fortunately Docker profiles abstract this issue away, so you don't need to worry about it if using Docker seccomp profiles.

`ptrace` is disabled by default and you should avoid enabling it. This is because it allows bypassing of seccomp. You can use [this script](https://gist.github.com/thejh/8346f47e359adecd1d53) to test for seccomp escapes through `ptrace`.

#### Differences between Docker versions

* Seccomp is supported as of Docker 1.10.

* Using the `--privileged` flag when creating a container with `docker run` disables seccomp in all versions of docker - even if you explicitly specify a seccomp profile. In general you should avoid using the `--privileged` flag as it does too many things. You can achieve the same goal with `--cap-add ALL --security-opt apparmor=unconfined --security-opt seccomp=unconfined`. If you need access to devices use `--device`.

* In docker 1.10-1.12 `docker exec --privileged` does not bypass seccomp. This may change in future versions https://github.com/moby/moby/issues/21984.

* In docker 1.12 and later, adding a capability may enable some appropriate system calls in the default seccomp profile. However, it does not disable apparmor.

### Using multiple filters

The only way to use multiple seccomp filters, as of Docker 1.12, is to load additional filters within your program at runtime. The kernel supports layering filters.

When using multiple layered filters, all filters are always executed starting with the most recently added. The highest precedence action returned is taken. See the man page for all the details: http://man7.org/linux/man-pages/man2/seccomp.2.html

### Misc

You can enable JITing of BPF filters (if it isn't already enabled) with the following command:

```
$ echo 1 > /proc/sys/net/core/bpf_jit_enable
```

There is no easy way to use seccomp in a mode that reports errors without crashing the program. However, there are several round-about ways to accomplish this. One such way is to use SCMP_ACT_TRAP and write your code to handle SIGSYS and report the errors in a useful way. Here is some information on [how Firefox handles seccomp violations](https://wiki.mozilla.org/Security/Sandbox/Seccomp).

### Further reading:

Very comprehensive presentation about seccomp that goes into more detail than this document.
https://lwn.net/Articles/656307/
http://man7.org/conf/lpc2015/limiting_kernel_attack_surface_with_seccomp-LPC_2015-Kerrisk.pdf

Chrome's DSL for generating seccomp BPF programs:
https://cs.chromium.org/chromium/src/sandbox/linux/bpf_dsl/bpf_dsl.h?sq=package:chromium&dr=CSs
