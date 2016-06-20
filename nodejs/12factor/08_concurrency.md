# 8 - Concurrency

Horizontal scalability with the processus model.

The app can be seen as a set of processus of different types
* web serveur
* worker
* cron

Each processus needs to be able to scale horizontally, it can have ot's own internal multiplexing.

## What does that mean for our application ?

The messageApp only have one type of processus (http server), it's doing the multiplexing using Node.js / Sails.js / Express.js web server.

[Previous](07_port_binding.md) - [Next](09_disposability.md)
