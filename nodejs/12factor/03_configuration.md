# 3 - Configuration

Configuration should be stored in the environment Ex:
credentials, database connection string, ...

## What does that mean for our application ?

In _config/connections.js_, we define the _mongo_ connection and use MONGO_URL environment variable to pass the mongo connection string.

```
module.exports.connections = {
  mongo: {
     adapter: 'sails-mongo',
     url: process.env.MONGO_URL'
  }
};
```

In _config/model.js_, we make sure the _mongo_ connection defined above is the one used.

```
module.exports.models = {
connection: mongo,
 migrate: 'safe'
};
```

Those changes enable to provide a different _MONGO_URL_ very easily as it's defined in the environment.

[Previous](02_dependencies.md) - [Next ](04_external_services.md)
