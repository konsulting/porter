#VERSION: 1.0.0
FROM alpine:3.7

ARG uid=1000

RUN apk add --update --no-cache \
    # for compilation
    g++ bash make zlib-dev libpng-dev \
	nodejs \
	yarn \
	git

RUN addgroup -g $uid node \
    && adduser -u $uid -G node -s /bin/sh -D node

WORKDIR /srv/app
