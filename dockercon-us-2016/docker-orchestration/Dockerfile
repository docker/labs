FROM ubuntu:14.04

RUN sudo apt-get update && apt-get -y install python-pip

RUN sudo pip install flask==0.10.1

COPY . /usr/bin

WORKDIR /usr/bin

EXPOSE 5000

CMD ["python", "./app.py"]
