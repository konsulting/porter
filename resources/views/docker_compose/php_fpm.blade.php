  php_fpm_{{ $version }}:
    build:
      context: .
      dockerfile: docker/php_fpm_{{ $version }}/Dockerfile
    networks:
      - porter
    ports:
      - {{ $port }}:9000
    volumes:
      - {{ $path }}:/srv/app
    environment:
       - DB_HOST={{ $db_host }}
