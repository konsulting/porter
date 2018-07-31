  nginx:
    build:
      context: .
      dockerfile: docker/nginx/Dockerfile
      cache_from:
        - konsulting/porter-nginx:latest
    image: konsulting/porter-nginx
    networks:
      - porter
    ports:
      - 80:80
      - 443:443
    volumes:
      - ./storage/config/nginx/nginx.conf:/etc/nginx/nginx.conf
      - ./storage/config/nginx/conf.d:/etc/nginx/conf.d
      - ./storage/ssl:/etc/ssl
      - ./storage/logs:/var/log
      - {{ $home }}:/srv/app
