# 4 - External services

Handle external services as external resources of the application.

Examples:
* database
* log services
* ...

This ensure the application is loosely coupled with the services so it can easily switch provider or instance if needed

## What does that mean for our application ?

At this point, the only external service the application is using is MongoDB database. The loose coupling is already done by the MONGO_URL used to pass the connection string.

If something wrong happens with our instance of MongoDB (assuming a single instance is used, which is generally a bad idea...), we can easily switch to a new instance, providing a new MONGO_URL environment variable and restarting the application.

[Previous](03_configuration.md) - [Next](05_build_release_run.md)
