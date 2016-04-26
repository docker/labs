FROM alpine:3.3

ENV WUNDERGROUND_API_KEY YourWundergroundApiKey

RUN apk add --update \
    python \
    python-dev \
    py-pip \
    build-base \
  && pip install virtualenv \
  && rm -rf /var/cache/apk/*

# install python libraries
RUN pip install requests \
  && pip install Flask \
  && pip install python-dateutil

# copy working-dir-context files to image
ADD . /app

# Flask Port
EXPOSE 5000

ENTRYPOINT ["python","/app/public-service.py"]
