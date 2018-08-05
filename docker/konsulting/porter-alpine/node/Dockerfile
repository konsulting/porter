FROM alpine:3.7

ARG uid=1000

RUN apk add --update --no-cache \
    bash \
	nodejs \ 
	yarn

RUN addgroup -g $uid node \
    && adduser -u $uid -G node -s /bin/sh -D node

WORKDIR /srv/app
