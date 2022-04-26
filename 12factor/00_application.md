# Build the application

To illustrate the 12 factors, we start by creating a simple Node.js application as a HTTP Rest API exposing CRUD verbs on a *message* model.

There is a couple of prerequisite to build this application
* [Node.js 10.13.0 (LTS)](https://nodejs.org/en/)
* [mongo 4.0](https://docs.mongodb.org/manual/installation/)

## Routes exposed

HTTP verb | URI | Action
----------| --- | ------
GET | /message | list all messages
GET | /message/ID | get message with ID
POST | /message | create a new message
PATCH | /message/ID | modify message with ID
DELETE | /message/ID | delete message with ID

## Setup

* Install Sails.js (it's to Node.js what RoR is to Ruby): `sudo npm install sails -g`
* Create the application:  `sails new messageApp && cd messageApp`
* Create the `message` api:  `sails generate api message`
* Launch the application: `sails lift`

## First tests

Create new messages

```
curl -XPOST http://localhost:1337/message?text=hello
curl -XPOST http://localhost:1337/message?text=hola
```

Get list of messages

```
curl http://localhost:1337/message

[
 {
   "text": "hello",
   "createdAt": "2015-11-08T13:15:15.363Z",
   "updatedAt": "2015-11-08T13:15:15.363Z",
   "id": 1
 },
 {
   "text": "hola",
   "createdAt": "2015-11-08T13:15:45.774Z",
   "updatedAt": "2015-11-08T13:15:45.774Z",
   "id": 2
 }
]
```

Modify a message

```
curl -XPATCH http://localhost:1337/message/1?text=hey
```

Delete a message

 ```
 curl -XDELETE http://localhost:1337/message/2
 ```

Get updates list of messages

```
curl http://localhost:1337/message

[
 {
   "text": "hey",
   "createdAt": "2015-11-08T13:15:15.363Z",
   "updatedAt": "2015-11-08T13:19:40.179Z",
   "id": 1
 }
]
```

[Next](01_codebase.md)
