# 12 - Admin processes

Admin process should be seen as a one-off process (opposed to long running processes that make up an application).

Usually used for maintenance task, though a REPL, admin process must be executed on the same release (codebase + configuration) than the application.

## What does that mean for our application ?

In the docker-compose file we could define an admin service that is ran at the same time as the application and in which we could jump (`docker exec -ti ADMIN_CONTAINER_ID bash`) to execute some admin tasks. The container is able to access all the other containers of the application (provided it belongs to the same networks)

[Previous](11_logs.md)
