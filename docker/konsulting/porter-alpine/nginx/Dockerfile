FROM alpine:3.7

RUN apk add --update nginx && mkdir -p /run/nginx

EXPOSE 80 443

STOPSIGNAL SIGTERM

CMD ["nginx", "-g", "daemon off;"]
