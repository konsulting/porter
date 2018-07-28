version: "3"
networks:
  porter:
    driver: bridge
services:
@foreach($activePhpVersions as $key => $version)
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
      - {{ $home }}:/srv/app
  node:
    build:
      context: .
      dockerfile: docker/node/Dockerfile
    user: node
    volumes:
      - {{ $home }}:/srv/app
    networks:
      - porter
