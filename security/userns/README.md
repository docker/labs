# Lab: User Namespaces

> **Difficulty**: Intermediate

> **Time**: Approximately 10 minutes


By default, the Docker daemon runs as *root*. This allows the daemon to create and work with the kernel structures required to start containers. However, it also presents potential security risks. This lab will walk you through implementing a more secure configuration utilizing *user namespaces*.

You will complete the following steps in this lab:

- [Step 1 - Daemon and container defaults](#defaults)
- [Step 2 - The `--user` flag](#user_flag)
- [Step 3 - Enabling **user namespaces**](#userns)

# Prerequisites

You will need all of the following to complete this lab:

- A Linux-based Docker Host running Docker 1.10 or higher
- Root access on the Docker Host

> **Note:** The instructions in this lab are tailored to a Docker Host running Ubuntu 15.10. An [open documented issue](https://github.com/moby/moby/issues/22633) exists with Ubuntu 16.04 Xenial .

# <a name="defaults"></a>Step 1: Daemon and container defaults

In this step you'll verify that the Docker daemon, and containers, run by default as root. You will also force a single container to run under a different security context.

You must perform this step while logged in as the **ubuntu** user.

1. Use the `ps` command to verify that the *Docker daemon* is currently running under the **root** user's security context.

   ```
   ubuntu@node:~$ ps aux | grep dockerd

   root      8715  0.0  1.0 352332 38820 ?        Ssl  12:56   0:01 /usr/bin/dockerd -H fd://
   ubuntu    8896  0.0  0.0   8216  2188 pts/0    S+   13:45   0:00 grep --color=auto dockerd
   ```

  The first line shows the Docker daemon (**dockerd**). The second line shows the `ps` command you just ran. The first column of the first line shows that the Docker daemon is running as **root**.

  > **Note:** If you are using a Docker Engine earlier than 1.12 you will need to substitute `dockerd` with `docker`.

2. Start a new container that runs the `id` command.

   ```
   ubuntu@node:~$ sudo docker run --rm alpine id

   Unable to find image 'alpine:latest' locally
   latest: Pulling from library/alpine
   e110a4a17941: Pull complete
   Digest: sha256:3dcdb92d7432d56604d4545cbd324b14e647b313626d99b889d0626de158f73a
   Status: Downloaded newer image for alpine:latest
   uid=0(root) gid=0(root) groups=0(root),1(bin),2(daemon),3(sys),4(adm),6(disk),10(wheel),11(floppy),20(dialout),26(tape),27(video)
   ```

  The last line of the output above shows that the container is running as root - `uid=0(root)` and `gid=0(root)`.

This step has shown you that the Docker daemon runs as root by default. You have also seen that new containers also start as root.

# <a name="user_flag"></a>Step 2: The `--user` flag

In this step you will start a new container and force it to run under the security context of the user that you are logged in as.

You should be logged in as the **ubuntu** user.

1. Issue the `id` command form the terminal of your Docker Host to determine the **uid** and **gid** of the user you are currently logged in as.

   ```
   ubuntu@node:~$ id

   uid=1000(ubuntu) gid=1000(ubuntu) groups=1000(ubuntu),4(adm),20(dialout)..<snip>
   ```

  The **uid** and **gid** in the above output are both "1000". Yours might be different. You will need your values in the next command.

2. Use the `docker run` command with the `--user` flag to force a new container to start with the **uid** and **gid** of your current user.

   ```
   ubuntu@node:~$ sudo docker run --rm --user 1000:1000 alpine id

   uid=1000 gid=1000
   ```

  The output shows that the container is running under **uid** 1000 and **gid** 1000. This proves that the container ran under the security context of a user and group that you specified.

There are many times when containers need to run under the root security context, while at the same time not having root access to the entire Docker Host. This is where user namespaces help!

# <a name="userns"></a>Step 3: User namespaces

User namespaces have been part of the Linux kernel for a while. They have been available in Docker since version 1.10 of the Linux Docker Engine. They allow the Docker daemon to create an isolated namespace that looks and feels like a root namespace. However, the `root` user inside of this namespace is mapped to a non-privileged **uid** on the Docker Host. This means that containers can effectively have root privilege inside of the user namespace, but have no privileges on the Docker Host.

In this step you'll see how to implement user namespaces.

> **Note:** It is not recommended to switch the Docker daemon back and forth between having user namespace mode enabled, and user namespace mode disabled. Doing this can cause issues with image permissions and visibility.

1. Stop the Docker Daemon

   ```
   ubuntu@node:~$ sudo systemctl stop docker
   ```

2. Start the Docker Daemon with user namespace support enabled.

   ```
   ubuntu@node:~$ sudo dockerd --userns-remap=default &
   ```

  If you are using a Docker 1.11 or earlier you will need to supplement `dockerd` with `docker daemon` in the previous command.

  This will start the Docker Daemon in the background using the default user namespace mapping where the **dockermap** user and group are created and mapped to non-privileged **uid** and **gid** ranges in the `/etc/subuid` and `/etc/subgid` files.

  The `/etc/subuid` and `/etc/subgid` files have a single-line entry for each user/group. Each line is formatted with three fields as follows -

  - User or group name
  - Subordinate user ID (uid) or group ID (gid)
  - Number of subordinate ID's available

3. Use the `docker info` command to verify that user namespace support is properly enabled.

   ```
   ubuntu@node:~$ sudo docker info

   Containers: 3
    Running: 1
    Paused: 0
    Stopped: 2
   Images: 2
   <snip>
   Docker Root Dir: /var/lib/docker/231072.231072
   <snip>
   ```

   The numbers at the end of the **Docker Root Dir** line indicate that the daemon is running inside of a user namespace. The numbers will match the subordinate user ID of the **dockermap** user as defined in the `/etc/subuid` file.

4. Check which images are stored in the daemon's local store.

   ```
   ubuntu@node:~$ sudo docker images
   REPOSITORY          TAG                 IMAGE ID            CREATED             SIZE
   ```

   The Docker daemon's local store is empty! This is despite the fact that the **alpine** image was pulled locally in a previous step. This is because this instance of the Docker daemon is running inside of a brand new namespace and has no visibility of anything outside of that namespace.

5. Now try running a new container in privileged mode.

   ```
   ubuntu@node:~$ sudo docker run --rm --privileged alpine id

   docker: Error response from daemon: Privileged mode is incompatible with user namespaces.
   See 'docker run --help'.
   ```

  As stated in the error response, *privileged* containers are not currently supported with user namespaces. But user namespaces for a container can be disabled by using the 'host' user namespace:
  
  ```
  ubuntu@node:~$ sudo docker run --rm --privileged --userns=host alpine id
  uid=0(root) gid=0(root) groups=0(root),1(bin),2(daemon),3(sys),4(adm),6(disk),10(wheel),11(floppy),20(dialout),26(tape),27(video)
  ubuntu@node:~$ 
  ```

6. Start a new container in interactive mode and mount the Docker Host's `/bin` directory as a volume.

   ```
   ubuntu@node:~$ sudo docker run -it --rm -v /bin:/host/bin busybox /bin/sh

   Unable to find image 'busybox:latest' locally
   latest: Pulling from library/busybox
   8ddc19f16526: Pull complete
   Digest: sha256:a59906e33509d14c036c8678d687bd4eec81ed7c4b8ce907b888c607f6a1e0e6
   Status: Downloaded newer image for busybox:latest
   ```

7. Use the `id` command to verify the security context the container is running under.

  The previous command should have attached you to the terminal of the newly created container. The following commands should be ran from inside of the container created in the previous step.

   ```
   / # id

   uid=0(root) gid=0(root) groups=10(wheel)
   ```

   The output above shows that the container is running under the root user's security context. Remember that this is only root within the scope of the namespace that the container is running in.

8. Try and delete a file that exists in the volume that you mounted from the Docker Host's filesystem into the container as a volume.

   ```
   / # rm host/bin/sh

   rm: remove 'sh'? y
   rm: can't remove 'sh': Permission denied
   ```

  The operation fails with a permission denied error. This is because the file you are trying to delete exists in the local filesystem of the Docker Host and the container does not have root access outside of the namespace that it exists in.

  If you perform the same operation - start the same container and attempt the same `rm` command - without user namespace support enabled, the operation will succeed.

# <a name="run_app"></a>Step 2: Clean-up

The following commands will clean up the user namespace you've been working in and restart the Docker daemon without user namespace support enabled.

   ```
   ubuntu@node:~$ sudo docker rm -f $(docker ps -aq)
   <snip>
   ubuntu@node:~$ sudo docker rmi $(docker images -q)
   <snip>

   ubuntu@node:~$ sudo killall dockerd -v

   ubuntu@node:~$ sudo systemctl start docker
   ```

> **Note:** If you are using a version of the Docker Engine prior to 1.12 you will have to substitute `dockerd` with `docker` in the previous `killall` command.

# Summary

In this lab you learned how to start the Docker daemon with user namespace support enabled. This started the daemon in a new namespace and mapped the root user inside of the namespace to a non-privileged user outside of the user namespace. This meant that the root user within the user namespace had full access to processes and containers within that namespace, but did not have elevated permissions outside of the namespace.

You proved that the Docker daemon was running within a user namespace using the `docker info` command. You saw that the root user inside of a the user namespace was unable to delete files that existed outside of the namespace.

# Additional Resources

You can refer to the following resources for more information and help:
- Docker: http://www.docker.com
