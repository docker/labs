# Setup our sample Node.js application

## Application details

* API HTTP Rest based on Node.js / [Sails.js](sailsjs.org)) and [MongoDB](https://www.mongodb.com/)
* A couple of prerequisite to run the application locally
  * [Node.js 4.4.5 (LTS)](https://nodejs.org/en/)
  * [mongo 3.2](https://docs.mongodb.org/manual/installation/)
* CRUD (Create / Read / Update / Delete HTTP verbs) on a “Message” model

HTTP verb | URI | Action
----------| --- | ------
GET | /message | list all messages
GET | /message/ID | get message with ID
POST | /message | create a new message
PUT | /message/ID | modify message with ID
DELETE | /message/ID | delete message with ID

## Setup

* install sailsjs (Sailsjs is for Node.js what RoR is to Ruby): ```sudo npm install sails -g``` (should install 0.12.3)
* create the  application:  ```sails new messageApp && cd messageApp```
* link with local MongoDB
  * usage of sails-mongo orm: ```npm install sails-mongo --save```
  * change configuration

```
config/model.js:
module.exports.models = {
connection: mongo,
 migrate: 'safe'
};
```


```
config/connections.js:
module.exports.connections = {
  mongo: {
     adapter: 'sails-mongo',
     url: process.env.MONGO_URL || 'mongodb://localhost/messageApp'
  }
};
```

* create API ```sails generate api message```
* run the application: ```sails lift```
* API available on localhost:1337

## Test the application in command line

* Get current list of messages
  * ```curl http://localhost:1337/message```

```
[]
```

* Create new messages
  * ```curl -XPOST http://localhost:1337/message?text=hello```
  * ```curl -XPOST http://localhost:1337/message?text=hola```
  
* Get list of messages
  * ```curl http://localhost:1337/message```

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
* Modify a message
  * ```curl -XPUT http://localhost:1337/message/5638b363c5cd0825511690bd?text=hey````

* Delete a message
  * ```curl -XDELETE http://localhost:1337/message/5638b381c5cd0825511690be````

* Get list of messages
  * ```curl http://localhost:1337/message```

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
