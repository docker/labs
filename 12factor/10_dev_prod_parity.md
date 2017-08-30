# 10 - Dev / Prod parity

The different environments must be as close as possible.

Docker is very good at reducing the gap as the same services can be deployed on the developer machine as they could on any Docker Hosts.

A lot of external services are available on the Docker Store and can be used in an existing application. Using those components enables a developer to use Postgres in development instead of SQLite or other lighter alternative. This reduces the risk of small differences that could show up later, when the app is on production.

This factor shows an orientation toward continuous deployment, where development can go from dev to production in a very short timeframe, thus avoiding the big bang effect at each release.


## What does that mean for our application ?

The docker-compose file we built so far can be ran on the local machine or on any Docker Host. So Docker really shines at this level as it handles everything for us.

The exact same application can run on each environment.

[Previous](09_disposability.md) - [Next](11_logs.md)
