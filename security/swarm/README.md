# Swarm Mode Security

# Lab Meta

> **Difficulty**: Beginner

> **Time**: Approximately 15 minutes

In this lab you'll build a new Swarm and view some of the built-in security
features of *Swarm mode*. These include *join tokens* and *client certificates*.

You will complete the following steps as part of this lab.

- [Step 1 - Create a new Swarm](#swarm_init)
- [Step 2 - Add a new Manager](#add_mgr)
- [Step 3 - Add a new Worker](#add_wrkr)
- [Step 4 - Rotate Join Keys](#rotate_join)
- [Step 5 - View certificates](#certs)
- [Step 6 - Rotate certificates](#rotate_certs)

# Prerequisites

You will need all of the following to complete this lab:

- Four Linux-based Docker hosts running **Docker 1.13** or higher and **not**
configured for Swarm Mode. You should use **node1**, **node2**, **node3**, and
**node4** from your lab.
- This lab was built and tested using Ubuntu 16.04

>NOTE: Things like IP addresses and Swarm *join tokens* will be different in
your lab. Remember to substitute the values shown here in the lab guide for the real values in your lab.

# <a name="swarm_init"></a>Step 1: Create a new Swarm

In this step you'll initialize a new Swarm and verify that the operation worked.

For this lab to work you will need your Docker hosts running in
*single-engine mode* and not in *Swarm mode*.

1. Execute the following command on **node1**.

    ```
    node1$ docker swarm init
    Swarm initialized: current node (kgwuvt1oqhqjsht0qcsq67rvu) is now a
    manager.

    To add a worker to this swarm, run the following command:

    docker swarm join \
    --token SWMTKN-1-4h5log5xpip966...y6gdy1-44v7nl9i0...k4fb8dlf21 \
    172.31.45.44:2377

    To add a manager to this swarm, run 'docker swarm join-token manager' and
    follow the instructions.

    ```

  The command above has created a brand new Swarm and made **node1** the first
  *manager* of the Swarm. The first manager of any Swarm is automatically made
  the *leader* and the *Certificate Authority (CA)* for the Swarm. If you
  already have a CA and do not want Swarm to generate a new one, you can use
  the `--external-ca` flag to specify an external CA.

2. Verify that the Swarm was created successfully and that **node1** is the
leader of the new Swarm with the following command.

    ```
    node1$ docker node ls
    ID                       HOSTNAME   STATUS  AVAILABILITY  MANAGER STATUS
    kgwuvt...0qcsq67rvu *    node1      Ready   Active        Leader
    ```

  The command above will list all nodes in the Swarm. Notice that the output
  only lists one node and that the node is also the *leader*.

3. Run a `docker info` command and view the Swarm related information.

    ```
    node1$ docker info
    ...
    <Snip>
    Swarm: active
      NodeID: kgwuvt1oqhqjsht0qcsq67rvu
      Is Manager: true
      ClusterID: ohgi9ctpbev24dl6daf7insou
      Managers: 1
      Nodes: 1
      Orchestration:
       Task History Retention Limit: 5
      Raft:
      Snapshot Interval: 10000
      Number of Old Snapshots to Retain: 0
      Heartbeat Tick: 1
      Election Tick: 3
     Dispatcher:
      Heartbeat Period: 5 seconds
     CA Configuration:
      Expiry Duration: 3 months
     ...
    ```

  The important things to note from the output above are; `nodeID`,
  `ClusterID`, `CA Configuration`.

It is important to know that the `docker swarm init` command performs at least
two important security related operations:
- It creates a new CA (unless you specify `--external-ca`) and creates a
key-pair to secure communications within the Swarm
- It creates two *join tokens* - one to join new *workers* to the Swarm, and the
other to join new *managers* to the Swarm.

We will look at these in the following steps.

# <a name="add_mgr"></a>Step 2: Add a new Manager

Now that you have a Swarm initialized, it's time to add another Manager.

In order to add a new Manager you must know the manager *join token* for the
Swarm you wish to join it to. The process below will show you how to obtain the
manager *join token* and use it to add **node2** as a new manager in the Swarm.

1. Use the `docker swarm join-token` command to get the *manager* join token.

    ```
    node1$ docker swarm join-token manager
    To add a manager to this swarm, run the following command:

    docker swarm join \
    --token SWMTKN-1-4h5log5xpip966c6c...z2cy6gdy1-7y6lqwu6...goyf26yyg2 \
    172.31.45.44:2377
    ```
  The output of the command gives you the full command, including the join
  token, that you can run on any Docker node to join it as a manager.

  > NOTE: The join token includes a digest of the root CA's certificate, as well as a
  randomly generated secret. The format is as follows:
  **SWMTKN-1-< digest-of-root-CA-cert>-< random-secret >**.

2. Copy and paste the command in to **node2**. Remember to use the command and
join token for your lab, and not the value shown in this lab guide.

    ```
    node2$ docker swarm join \
    --token SWMTKN-1-4h5log5xpip966c6c...z2cy6gdy1-7y6lqwu6...goyf26yyg2 \
    172.31.45.44:2377

    This node joined a swarm as a manager.
    ```

3. Run the `docker node ls` command from either **node1** or **node2** to list
the nodes in the Swarm.

    ```
    node1$ docker node ls
    ID                      HOSTNAME   STATUS  AVAILABILITY  MANAGER STATUS
    ax2cmh63...tvjp8trs4    node2      Ready   Active        Reachable
    kgwuvt1o...qcsq67rvu *  node1      Ready   Active        Leader
    ```

The *join token* used in the commands above will join any node to your Swarm as
a *manager*. This means it is vital that you keep the join tokens private -
anyone in possession of it can join nodes to the Swarm as managers.

# <a name="add_wrkr"></a>Step 3: Add a new Worker

Adding a worker is the same process as adding a manager. The only difference is
the token used. Every Swarm maintains one *manager* join token and one
*worker* join token.

1. Run a `docker swarm join-token` command from any of the managers in your
Swarm to obtain the command and token required to add a new worker node.

    ```
    node1$ docker swarm join-token worker
    To add a worker to this swarm, run the following command:

    docker swarm join \
    --token SWMTKN-1-4h5log5xpip966c6c...z2cy6gdy1-44v7nl9...b8dlf21 \
    172.31.45.44:2377
    ```
  Notice that the join token for managers and workers share some of the same
  values. Both start with "SWMTKN-1", and both share the same Swarm root CA
  digest. It is only the last part of the token that determines if
  the token is for a manager or worker.

2. Switch to **node3** and paste in the command from the previous step.

    ```
    node3$ docker swarm join \
    --token SWMTKN-1-4h5log5xpip966c6c...z2cy6gdy1-44v7nl9...b8dlf21 \
    172.31.45.44:2377

    This node joined a swarm as a worker.
    ```

3. Switch back to one of the manager nodes (**node1** or **node2**) and run a
`docker node ls` command to verify the node was added as a worker.
    ```
    node1$ docker node ls
    ID                   HOSTNAME   STATUS  AVAILABILITY  MANAGER STATUS
    ax2cm...vjp8trs4     node2      Ready   Active        Reachable
    kgwuv...csq67rvu *   node1      Ready   Active        Leader
    mfg9d...inwonsjh     node3      Ready   Active
    ```

    The output above shows that **node3** was added to the Swarm and is
    operating as a worker - the lack of a value in the **MANAGER STATUS**
    column indicates that the node is a *worker*.

# <a name="rotate_join"></a>Step 4: Rotate Join Keys

In this step you will rotate the Swarms *worker* join-key. This will invalidate
the worker join-key used in previous steps. It will not affect the status of
workers already joined to the Swarm, this means all existing workers will
continue to be valid workers in the Swarm.

You will test that the *rotate operation* succeeded by attempting to add a new
worker with the old key. This operation will fail. You will then retry the
operation with the new key. This time it will succeed.

1. Rotate the existing worker key by execute the following command from either
of the Swarm managers.

    ```
    node1$ docker swarm join-token --rotate worker
    Successfully rotated worker join token.

    To add a worker to this swarm, run the following command:

      docker swarm join \
      --token SWMTKN-1-4h5log5xpip...cy6gdy1-55k4ywd...z5xtns4eq \
      172.31.45.44:2377
    ```

    Notice that the new join token still starts with `SWMTKN-1` and keeps the
    same digest of the Swarms root CA `4h5log5...`. It is only the last part of
    the token that has changed. This is because the new token is still a Swarm
    join token for the same Swarm. The system has only rotated the *secret*
    used to add new workers (the last portion).

2. Log on to **node4** and attempt to join the Swarm using the **old** join
token. You should be able to find the old join token in the terminal window of
**node3** from a previous step.

    ```
    node4$ docker swarm join \
    --token SWMTKN-1-4h5log5xpi...duz2cy6gdy1-44v7nl9...4fb8dlf21 \
    172.31.45.44:2377

    Error response from daemon: rpc error: code = 3 desc = A valid join token
    is necessary to join this cluster
    ```
  The operation fails because the join token is no longer valid.

3. Retry the previous operation using the new join token given as the output to
the `docker swarm join-token --rotate worker` command in a previous step.

    ```
    node4$ docker swarm join \
    --token SWMTKN-1-4h5log5...wzqlduz2cy6gdy1-55k4ywd...xtns4eq \
    172.31.45.44:2377

    This node joined a swarm as a worker.
    ```

Rotating join tokens is something that you will need to do if you suspect your
existing join tokens have been compromised. It is important that you manage
your join-tokens carefully. This is because unauthorized nodes joining the
Swarm is a security risk.

# <a name="certs"></a>Step 5: View certificates

Each time a new *manager* or *worker* joins the Swarm it is issued with a
*client certificate*. This client certificate is used in conjunction with the
existing Swarm public key infrastructure (PKI) to authenticate the node and
encrypt communications.

There are three important things to note about the *client certificate*:
1. It specifies which Swarm the node is an authorized member of
2. It contains the node ID
3. It specifies the role the node is authorized to perform in the Swarm
(*worker* or *manager*)


Execute the following command from any node in your Swarm to view the nodes
*client certificate*.


    node1$ openssl x509 -in /var/lib/docker/swarm/certificates/swarm-node.crt -text

    Certificate:
      Data:
          Version: 3 (0x2)
          Serial Number:
              59:53:84:47:3a:2d:15:5b:f0:39:46:93:dd:21:68:ad:70:62:40:d1
      Signature Algorithm: ecdsa-with-SHA256
          Issuer: CN=swarm-ca
          Validity
              Not Before: Mar 14 11:42:00 2017 GMT
              Not After : Jun 12 12:42:00 2017 GMT
          Subject: O=ohgi9...insou, OU=swarm-manager, CN=kgwuvt...csq67rvu
          ...

The important things to note about the output above are the three fields on the
bottom line:

- The Organization (O) field contains the Swarm ID
- The Organization Unit (OU) field contains the nodes *role*
- The Common Name (CN) field contains the nodes ID

These three fields make sure the node operates in the correct Swarm, operates in
the correct role, and is the node it says it is.

You can use the `docker swarm update --cert-expiry <TIME PERIOD>` command to
change frequency at which the client certificates in the Swarm are renewed. The
default is 90 days (3 months).

# <a name="rotate_certs"></a>Step 6: Rotate certificates

In this step you'll view the existing certificate rotation period for your
Swarm, and then alter that period.

Perform the following commands from a manager node in your Swarm.

1. Use the `docker info` command to view the existing certificate rotation
period enforced in your Swarm.

```
node1$ docker info
Swarm: active
 NodeID: kgwuvt1oqhqjsht0qcsq67rvu
 Is Manager: true
 ClusterID: ohgi9ctpbev24dl6daf7insou
 Managers: 2
 Nodes: 4
 Orchestration:
  Task History Retention Limit: 5
 Raft:
  Snapshot Interval: 10000
  Number of Old Snapshots to Retain: 0
  Heartbeat Tick: 1
  Election Tick: 3
 Dispatcher:
  Heartbeat Period: 5 seconds
 CA Configuration:
  Expiry Duration: 3 months
```  

  The last two lines of the output above show that the current rotation period
  (**Expiry Duration**) is **3 months**.

2. Use the `docker swarm update` command to change the rotation period.

```
node1$ docker swarm update --cert-expiry 168h
Swarm updated.
```

  The `--cert-expiry` flag accepts time periods in the format `00h00m00s`,
  where `h` is for hours, `m` is for minutes, and `s` is for seconds. The
  example above sets the rotation period to 168 hours (7 days).

3. Run another `docker info` to check that the value has changed.

```
node1$ docker info
Swarm: active
 NodeID: kgwuvt1oqhqjsht0qcsq67rvu
 Is Manager: true
 ClusterID: ohgi9ctpbev24dl6daf7insou
 Managers: 2
 Nodes: 4
 Orchestration:
  Task History Retention Limit: 5
 Raft:
  Snapshot Interval: 10000
  Number of Old Snapshots to Retain: 0
  Heartbeat Tick: 1
  Election Tick: 3
 Dispatcher:
  Heartbeat Period: 5 seconds
 CA Configuration:
  Expiry Duration: 7 days
```  

**Congratulations**, you have completed this lab on basic Swarm security.
