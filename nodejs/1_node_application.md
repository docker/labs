# Application details

* API HTTP Rest - Node.js (Sails.js) / MongoDB
* Prerequisite
  * nodejs 4.4.5 (LTS) - https://nodejs.org/en/
  * mongo 3.2 - https://docs.mongodb.org/manual/installation/
* CRUD on a “Message” model

HTTP verb | URI | Action
----------| --- | ------
GET | /message | list all messages
GET | /message/ID | get message with ID
POST | /message | create a new message
PUT | /message/ID | modify message with ID
DELETE | /message/ID | delete message with ID


# Setup

* usage of sailsjs framework (RoR of Node.js)
  * install sailsjs: sudo npm install sails -g (should install 0.12.3)
  * create the  application:  sails new messageApp && cd messageApp
* link with local MongoDB
  * usage of sails-mongo orm: npm install sails-mongo --save
  * change configuration

```
config/model.js:
module.exports.models = {
connection: mongo,
 migrate: 'safe'
};```

```
config/connections.js:
module.exports.connections = {
  mongo: {
     adapter: 'sails-mongo',
     url: process.env.MONGO_URL || 'mongodb://localhost/messageApp'
  }
};
```

* create API: sails generate api message
* run the application: sails lift
* API available on localhost:1337

# Example

curl http://localhost:1337/message

    []

curl -XPOST http://localhost:1337/message?text=hello
curl -XPOST http://localhost:1337/message?text=hola
curl http://localhost:1337/message

```
[
  {
    "text": "hello",
    "createdAt": "2015-11-08T13:15:15.363Z",
    "updatedAt": "2015-11-08T13:15:15.363Z",
    "id": "5638b363c5cd0825511690bd"
  },
  {
    "text": "hola",
    "createdAt": "2015-11-08T13:15:45.774Z",
    "updatedAt": "2015-11-08T13:15:45.774Z",
    "id": "5638b381c5cd0825511690be"
  }
]
```

curl -XPUT http://localhost:1337/message/5638b363c5cd0825511690bd?text=hey
curl -XDELETE http://localhost:1337/message/5638b381c5cd0825511690be
curl http://localhost:1337/message

```
[
  {
    "text": "hey",
    "createdAt": "2015-11-08T13:15:15.363Z",
    "updatedAt": "2015-11-08T13:19:40.179Z",
    "id": "5638b363c5cd0825511690bd"
  }
]
```
