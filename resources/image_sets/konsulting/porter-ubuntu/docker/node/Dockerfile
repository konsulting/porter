#VERSION: 1.3.0
FROM alpine:3.13

ARG uid=1000

RUN apk add --update --no-cache \
    # for compilation
    g++ bash make zlib-dev libpng-dev \
	nodejs \
	npm \
	yarn \
	git

RUN addgroup -g $uid node \
    && adduser -u $uid -G node -s /bin/sh -D node

WORKDIR /srv/app
