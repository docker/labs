# messageApp

* Go into _messageApp_ folder `cd messageApp`
* Build the docker image `docker build -t message-app:v0.2 .`
* Run all message-app services with _docker-compose_ `docker-compose up -d`
* Check the service _app_ logs `docker-compose logs --follow app` (ctrl-c to exit)
* Insert records `curl -XPOST http://localhost:8000/message?text=hello` and  `curl -XPOST http://localhost:8000/message?text=hola`
* Read inserted records `curl http://localhost:8000/messages`
* Stop all message-app services `docker-compose down`
