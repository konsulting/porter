  nginx:
    build:
      context: {{ $imageSet->getDockerContext() }}
      dockerfile: nginx/Dockerfile
    image: {{ $imageSet->firstByServiceName('nginx')->getName() }}
    networks:
      - porter
    ports:
      - 80:80
      - 443:443
    volumes:
      - {{ $home }}:/srv/app:delegated
      - {{ $libraryPath }}/config/nginx/nginx.conf:/etc/nginx/nginx.conf
      - {{ $libraryPath }}/config/nginx/conf.d:/etc/nginx/conf.d
      - {{ $libraryPath }}/ssl:/etc/ssl
    restart: unless-stopped
