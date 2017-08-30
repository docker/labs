# Secrets

# Lab Meta

> **Difficulty**: Intermediate

> **Time**: Approximately 15 minutes

In this lab you'll learn how to create and manage *secrets* with Docker.

You will complete the following steps as part of this lab.

- [Step 1 - Create a Secret](#create)
- [Step 2 - Manage Secrets](#manage)
- [Step 3 - Access the secret within an app](#use)
- [Step 4 - Clean-up](#clean)

In this lab the terms *service task* and *container* are used interchangeably.
In all examples in the lab a *service tasks* is a container that is running as
part of a service.

# Prerequisites

You will need all of the following to complete this lab:

- A Docker Swarm cluster running **Docker 1.13** or higher

# <a name="create"></a>Step 1: Create a Secret

In this step you'll use the `docker secret create` command to create a new
*secret*.

Perform the following command from a *manager* node in your Swarm. This lab will assume that you are using **node1** in your lab.

1. Create a new text file containing the text you wish to use as your secret.

  ```
  node1$ echo "secrets are important" > sec.txt
  ```

  The command shown above will create a new file called `sec.txt` in your
  working directory containing the string **secrets are important**. The text
  string in the file is arbitrary but should be kept secure. You should follow
  any existing corporate guidelines about keeping secrets safe.

2. Confirm that the file was created.

  ```
  node1$ ls -l
  total 4
  -rw-r--r-- 1 root root 10 Mar 21 18:40 sec.txt
  ```

3. Use the `docker secret create` command to create a new secret using the file
created in the previous step.

  ```
  node1$ docker secret create sec1 ./sec.txt
  ftu76ghgsk7f9fmcrj3wx3xcd
  ```

  The return code of the command is the ID of the newly created secret.

Congratulations. You have created a new secret called `sec1`.

If you created the secret from a remote Docker client, it would be sent to a
manager node in the Swarm over a mutual TLS Connection. Once the secret is
received on the manager node it is securely stored in the Swarm's Raft store
using the Swarm's native encryption.

You can now delete the `sec.txt` file used to create the secret.

# <a name="manage"></a>Step 2: Manage Secrets

In this step you'll use the `docker secret` sub-command to list and inspect
secrets.

Before going any further it's important to note that once a secret is created
it is securely stored in the Swarm's encrypted Raft store. This means that you
cannot view it in plain text using the `docker secret` command.

Perform all of the following commands from a Swarm *manager*.  The lab assumes you will be using **node1** in your lab.

1. List existing secrets with the `docker secret ls` command.

  ```
  node1$ docker secret ls
  ID                     NAME      CREATED             UPDATED
  ftu76ghg...rj3wx3xcd   sec1      11 seconds ago      11 seconds ago
  ```

2. Inspect the **sec1** secret.

  ```
  node1$ docker secret inspect sec1
  [
    {
        "ID": "ftu76ghgsk7f9fmcrj3wx3xcd",
        "Version": {
            "Index": 113
        },
        "CreatedAt": "2017-03-21T18:41:08.790769302Z",
        "UpdatedAt": "2017-03-21T18:41:08.790769302Z",
        "Spec": {
            "Name": "sec1"
        }
    }
  ]
  ```

  Notice that the `docker secret inspect` command does not display the
  unencrypted contents of the secret.

You can use the `docker secret rm` command to delete secrets. To delete the
**sec1** secret you would use the command `docker secret rm sec1`. **Do not
delete the sec1 secret as you will use it in the next section.**


# <a name="use"></a>Step 3: Access the secret within an app

In this step you'll deploy a service and grant it access to the secret. You'll
then `exec` on to a task in the service and view the unencrypted contents of the
 secret.

Perform the following commands from a *manager* node in the Swarm and be sure
to remember that the outputs of the commands might be different in your lab.
E.g. service tasks in your lab might be scheduled on different nodes to those
shown in the examples below.

1. Create a new service and attach the `sec1` secret.

  ```
  node1$ docker service create --name sec-test --secret="sec1" redis:alpine
  p858ush7oeei8647na2xa12sc
  ```

  This command creates a new service called **sec-test**. The service has a
  single task (container), is given access to the **sec1** secret and is based
  on the `redis:alpine` image.

2. Verify the service is running.

  ```
  node1$ docker service ls
  ID             NAME       MODE         REPLICAS   IMAGE
  p858ush7oeei   sec-test   replicated   1/1        redis:alpine
  ```

3. Inspect the `sec-test` service to verify that the secret is associated with
it.

  ```
  node1$ docker service inspect sec-test
  [
    {
        "ID": "p858ush7oeei8647na2xa12sc",
        "Version": {
            "Index": 116
        },
        "CreatedAt": "2017-03-21T19:37:52.254797962Z",
        "UpdatedAt": "2017-03-21T19:37:52.254797962Z",
        "Spec": {
            "Name": "sec-test",
            "TaskTemplate": {
                "ContainerSpec": {
                    "Image": "redis:alpine@sha256:9cd405cd...fb4ec7bdc3ee7",
                    "DNSConfig": {},
                    "Secrets": [
                        {
                            "File": {
                                "Name": "sec1",
                                "UID": "0",
                                "GID": "0",
                                "Mode": 292
                            },
                            "SecretID": "ftu76ghgsk7f9fmcrj3wx3xcd",
                            "SecretName": "sec1"
                            <Snip>
  ```

  The output above shows that the `sec1` secret (ID:ftu76ghgsk7f9fmcrj3wx3xcd)
  is successfully associated with the `sec-test` service. This is important as
  it is what ultimately grants tasks within the service access to the secret.

4. Obtain the name of any of the tasks in the `sec-test` service (if you've been
following along there will only be one task running in the service).

  ```
  //Run the following docker service ps command to see which node
  the service task is running on.

  node1$ docker service ps sec-test
  ID          NAME        IMAGE         NODE    DESIRED STATE  CURRENT STATE   
  9qqp...htd  sec-test.1  redis:alpine  node1   Running        Running 8 mins..

  //Log on to the node running the service task (node1 in this example, but
    might be different in your lab) and run a docker ps command.

  node1$ docker ps --filter name=sec-test
  CONTAINER ID    IMAGE                     COMMAND                  CREATED   STATUS      PORTS      NAMES
  5652c1688f40    redis@sha256:9cd..c3ee7   "docker-entrypoint..."   15 mins   Up 15 mins  6379/tcp   sec-test.1.9qqp...vu2aw
  ```

  You will use the `CONTAINER ID` from the output above in the next step.

  > NOTE: The two commands above start out by listing all the tasks in the
  `sec-test` service. Part of the output of the first command shows the `NODE`
  that each task is running on - in the example above this was a single task
  running on **node1**. The next command (`docker ps`) lists all running
  containers on **node1** and filters the results to show just the containers
  where the name starts with **sec-test** - this means that only containers
  (tasks) that are part of the `sec-test` service are displayed.

5. Use the `docker exec` command to get a shell prompt on to the `sec-test`
service task. Be sure to substitute the Container ID in the command below with
a the container ID form your environment (see output of previous step).

  ```
  node1$ docker exec -it 5652c1688f40 sh
  data#
  ```

  The `data#` prompt is a shell prompt inside the service task.

6. List the contents of the container's `/run/secrets` directory.

  ```
  node1$ ls -l /run/secrets
  total 4
  -r--r--r--  1   root   root     10 Mar 21 19:37 sec1
  ```

  Secrets are only shared to *service tasks/containers* that are granted access
  to them, and the secrets are shared with the *service task* via the TLS
  connections that already exists between nodes in the Swarm. Once a *node* has
  a secret it mounts it as a regular file into an in-memory filesystem inside
  the authorized service task (container). This file is mounted at
  `/run/secrets` with the same name as the secret. In the example above, the
  `sec1` secret is mounted as a file called **sec1**.

7. View the unencrypted contents of the *secret*.

  ```
  node1$ cat /run/secrets/sec1
  secrets are important
  ```

It's important to note several things about this unencrypted secret.

- The secret is only made available to services that have been specifically
granted access to it (in our example this was via the `docker service create`
  command).
- The secret is issued to the service task by a manager in the Swarm via a
mutually authenticated TLS connection.
- Service tasks and nodes cannot request a secret - secrets are always issued
to the node/task by a manager as part of a service deployment or update.
- Secrets are only ever mounted to in-memory filesystems inside of authorized
containers/tasks and are never persisted to disk on worker nodes or containers.
- Nodes do not have access to the unencrypted secret.
- Other tasks and containers on the same node do not get access to the secret.
- As soon as a node is no longer running a task for a service it will delete
the secret from memory.

**Congratulations**, you have completed this lab on Secrets management.

# <a name="clean"></a>Step 5: Clean-up

In this step you will remove all secrets and services,as well as clean up any other artifacts created in this lab.


1. Remove all services on the host.

   This command will remove **all** services on your Docker host. Only perform this step if you know you know you do not need any of the services running on your system.

   ```
   $ docker service rm $(docker service ls -q)
   <Snip>
   ```
2. Remove all secrets on the host.

   This command will remove **all** secrets on your Docker host. Only perform this step if you know you will not use these secrets again.

   ```
   $ docker secret rm $(docker secret ls -q)
   <Snip>
   ```

3. If you haven;t already done so, delete the file that you used as the source of the secret data in Step 1. The lab assumed this was **node1** in your lab.

   ```
   $ rm sec.txt
   ```
