# Part 3 - Running the SQL Server Container

We now have a SQL Server Express image, packaged with our database. The database image was built in [Part 2](part-2.md), using the Dacpac we generated in [Part 1](part-1.md). We can use that container to spin up a database in different ways

## In Development - Creating an Empty Database

The image can be used in development environments where a fresh database is needed for working on new app features, and you want to easily reset the data to an initial state. In this scenario you don't want to persist data between containers, so you can just start a database container:

```Docker
docker run -d --rm -p 1433:1433 --name assets-db assets-db
```

When the container starts, it will run the deployment script, find there are no existing database files and create a new database. You can connect to SQL Server inside the container using SQL Server Management Studio or Server Explorer in Visual Studio. 

> Note: the current limitation in Windows networking means you can't access published ports from a Docker container using the engine's localhost address (see [Published Ports On Windows Containers Don't Do Loopback](https://blog.sixeyed.com/published-ports-on-windows-containers-dont-do-loopback/)). 

To connect we can fetch the container's IP address by running `inspect`:

```PowerShell
> $ip = docker inspect --format '{{ .NetworkSettings.Networks.nat.IPAddress }}' assets-db
> $ip
172.24.192.132
```

In my case the IP address is `172.24.192.132`, which is the server name for connections - yours will be different. You need to use SQL Server Authentication with the `sa` credentials, and you should see the `AssetsDB` database listed:

![Connecting to AssetsDB](./img/connection-settings.png)

You can run some SQL to insert test data like this:

```SQL
INSERT INTO Assets (AssetTypeId, LocationId, PurchaseDate, PurchasePrice, AssetTag, AssetDescription)
VALUES (1, 1, '2016-11-14', '1999.99', 'SC0001', 'New MacBook with Emoji Bar');

INSERT INTO Assets (AssetTypeId, LocationId, PurchaseDate, PurchasePrice, AssetTag, AssetDescription)
VALUES (1, 1, '2016-11-14', '800', 'SC0002', 'Logitech Office Cam');
```

And when you `SELECT` from the `Assets` table you'll see the new rows there. 

The data is being stored in a volume, which means the `MDF` and `LDF` files are somewhere on the the host's disk. But because we ran the container with the [--rm](https://docs.docker.com/engine/reference/run/#/clean-up---rm) option, the volume will be removed when the container stops - so this is an ephemeral container. 

We can see that if we kill the container, and start a new one with the same IP address as the original:

```PowerShell
docker kill assets-db
docker run -d --rm -p 1433:1433 --name assets-db --ip $ip assets-db
```

Refresh your SQL client connection, repeat the `SELECT * FROM Assets` query and you'll see the table is empty - the old data was lost when we killed the container and its volume was removed. The new container starts with a new database.

## In Test - Creating a Reusable Database

To store the data permanently, we just need to map the database volume to a location on the host. The first time we run a container, it will create the data and log files in the host directory. If we replace the container and use the same volume mount, the new container will attach the existing database and the data will be preserved.  

The `run` command is essentially the same, we just lose the `--rm` option and use the `-v` option to mount a volume. [Mounting a host directory as a volume](https://docs.docker.com/engine/tutorials/dockervolumes/#/mount-a-host-directory-as-a-data-volume) just means that when processes in a container think they're accessing files on the local filesystem, it's actually a symlink and the files are on the host. In this case, when SQL Server uses the `MDF` file in `C:\databases` in the container, it's actually using the file in `C:\databases\assets` on ths host:

```PowerShell
docker kill assets-db
mkdir C:\databases\assets
docker run -d -p 1433:1433 --name assets-db --ip $ip -v C:\databases\assets:C:\database assets-db
```

When the container has started, you can verify that the new database is created and the files are written to the host directory by listing the contents from the host:

```PowerShell
> ls C:\databases\assets\

    Directory: C:\databases\assets

Mode                LastWriteTime         Length Name
----                -------------         ------ ----
-a----       11/14/2016   1:30 PM        8388608 AssetsDB_Primary.ldf
-a----       11/14/2016   1:30 PM        8388608 AssetsDB_Primary.mdf
```

Now you can insert rows into the `Assets` table, and the data will be stored outside of the container. You can replace the container without changing the schema - say you rebuild it with a new version of the base image to get the latest Windows updates. As long as you use the same volume mapping as the previous container, you'll retain all the data:

```PowerShell
docker kill assets-db
docker run -d --rm -p 1433:1433 --name assets-db --ip $ip -v C:\databases\assets:C:\database assets-db
```

This is a new container with a new file system, but the database location is mapped to the same host directory as the previous container. When the new container starts, it attaches the database so all the existing data is available. You can check that by executing a query in a SQL client, or by running one in the container directly:

```PowerShell
> docker exec -t assets-db powershell.exe -Command "Invoke-SqlCmd -Query 'SELECT * FROM Assets' -Database AssetsDB"

AssetId          : 1
AssetTypeId      : 1
LocationId       : 1
PurchaseDate     : 11/14/2016 12:00:00 AM
PurchasePrice    : 1999.99
AssetTag         : SC0001
AssetDescription : New MacBook with Emoji Bar

AssetId          : 2
AssetTypeId      : 1
LocationId       : 1
PurchaseDate     : 11/14/2016 12:00:00 AM
PurchasePrice    : 800.00
AssetTag         : SC0002
AssetDescription : Logitech Office Cam
```

## In Production - Using Shared Storage

For production database use, you can use exactly the same image and the same principle as for test environments, but you may want to use a different type of volume mount. 

In small-scale single-host scenarios, you can mount your database volume from a RAID array on the local server. That gives you data redundancy but not process redundancy - if you lose a disk you won't lose data, but if the server goes down your database won't be accessible.

In high-availability scenarios, you'll need process redundancy too, so if the server hosting your database container goes down, you can spin up a new container on a different server, but retain all the committed data. That means using a shared storage driver, where the database directory is available from multiple servers, so you can map the same volume from any host.

Docker has a [volume plugin framework](https://docs.docker.com/engine/extend/plugins_volume/) which third parties can use to support Docker volumes on their shared storage solutions. The plugin you choose depends on your infrastructure, but the [volume plugin list](https://docs.docker.com/engine/extend/legacy_plugins/#/volume-plugins) has support for:
- [vSphere storage](https://github.com/vmware/docker-volume-vsphere)
- [NetApp](https://github.com/NetApp/netappdvp)
- [Azure File Storage](https://github.com/Azure/azurefile-dockervolumedriver)
- [Google Cloud](https://github.com/mcuadros/gce-docker)
- [HPE 3Par](https://github.com/hpe-storage/python-hpedockerplugin/) 

and lots more.

## Next

- [Part 4 - Upgrading the SQL Server Database](part-4.md)