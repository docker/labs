# 10 - Dev / Prod parity

The different environments must be as close as possible.

Docker is very good at reducing the gap as the same services can be deployed on the developer machine as they could on any Docker Hosts.

A lot of external services are available on the Docker Hub and can be used in an existing application. Using those components enables, for instance, a developer to use Postgres in development instead of Sqlite or other lighter alternative. He then reduces the risk of small differences to show up later.

This factor shows an orientation toward continuous deployment, where development can go from dev to production in a very short timeframe, thus avoiding the big bang effect at each release.


## What does that mean for our application ?

The docker-compose file we built so far can be ran on the local machine or on any Docker Host. So Docker really shines at this level as it handles everything for us.

[Previous](09_disposability.md) - [Next](11_logs.md)
