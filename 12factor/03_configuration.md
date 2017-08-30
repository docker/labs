# 3 - Configuration

Configuration (credentials, database connection string, ...) should be stored in the environment.

## What does that mean for our application ?

In _config/connections.js_, we define the _mongo_ connection and use MONGO_URL environment variable to pass the mongo connection string.

```node
module.exports.connections = {
  mongo: {
    adapter: 'sails-mongo',
    url: process.env.MONGO_URL
  }
};
```

In _config/model.js_, we make sure the _mongo_ connection defined above is the one used.

```node
module.exports.models = {
  connection: 'mongo',
  migrate: 'safe'
};
```

Those changes enable to provide a different _MONGO_URL_ very easily as it's defined in the environment.

[Previous](02_dependencies.md) - [Next ](04_external_services.md)
