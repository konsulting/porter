version: "3"
networks:
  porter:
    driver: bridge
services:
@foreach(settings('php_versions') as list($version, $port))
  @include('docker_compose.php_fpm')
  @include('docker_compose.php_cli')
@endforeach
  nginx:
    build:
      context: .
      dockerfile: docker/nginx/Dockerfile
    networks:
      - porter
    ports:
      - 80:80
      - 443:443
    volumes:
      - ./storage/nginx/conf.d:/etc/nginx/conf.d
      - ./storage/ssl:/etc/ssl
      - ./storage/log:/var/log
      - {{ $path }}:/srv/app
  node:
    build:
      context: .
      dockerfile: docker/node/Dockerfile
    user: node
    volumes:
      - {{ $path }}:/srv/app
    networks:
      - porter
