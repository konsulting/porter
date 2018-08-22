  nginx:
    build:
      context: ./docker/{{ $imageSet }}
      dockerfile: nginx/Dockerfile
      cache_from:
        - {{ $imageSet }}-nginx:latest
    image: {{ $imageSet }}-nginx
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
      - {{ $home }}:/srv/app:delegated
    restart: unless-stopped
