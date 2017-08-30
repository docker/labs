FROM node:8.2.1-alpine

WORKDIR /code

RUN npm install -g nodemon@1.11.0

COPY package.json /code/package.json
RUN npm install && npm ls
RUN mv /code/node_modules /node_modules

COPY . /code

CMD ["npm", "start"]
