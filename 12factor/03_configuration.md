# 3 - Configuration

Configuration (credentials, database connection string, ...) should be stored in the environment.

## What does that mean for our application ?

In _config/datastores.js_, we define the _mongo_ datastore and use MONGO_URL environment variable to pass the mongo datastore string (we'll see how to set MONGO_URL in step [5 - Build / Release / Run](05_build_release_run.md)).

```node
module.exports.datastores = {
  ...
  default: {
    ...
  },
  // define mongo datastore
  mongo: {
    adapter: 'sails-mongo',
    url: process.env.MONGO_URL
  }
};
```

In _config/models.js_, we make sure the _mongo_ datastore defined above is the one used. Also, we need to set the _id_ attribute properly.

```node
module.exports.models = {
  ...
  attributes: {
    ...
    // set mongo id format
    id: { type: 'string', columnName: '_id' },
    ...
  },
  // use mongo datastore instead of default
  datastore: 'mongo',
  migrate: 'safe'
};
```

Those changes enable to provide a different _MONGO_URL_ very easily as it's defined in the environment.

In order to run in production, Sails requires us to set at least one URL in the `onlyAllowOrigins` array in _config/sockets.js_. We'll read the values from environment variables `ALLOW_ORIGIN` and `PORT` and fallback to default values in case they are not set.

```node
module.exports.sockets = {
  ...
  onlyAllowOrigins: [ "http://" + (process.env.ALLOW_ORIGIN || "localhost") + ":" + (process.env.PORT || "1337") ]
};
```

[Previous](02_dependencies.md) - [Next ](04_external_services.md)
