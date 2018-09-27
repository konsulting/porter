  nginx:
    build:
      context: {{ $dockerContext }}
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
      - {{ $libraryPath }}/config/nginx/nginx.conf:/etc/nginx/nginx.conf
      - {{ $libraryPath }}/config/nginx/conf.d:/etc/nginx/conf.d
      - {{ $libraryPath }}/ssl:/etc/ssl
      - {{ $home }}:/srv/app:delegated
    restart: unless-stopped
