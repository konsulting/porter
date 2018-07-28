  php_fpm_{{ $version->safe }}:
    build:
      context: .
      dockerfile: docker/php_fpm_{{ $version->safe }}/Dockerfile
    networks:
      - porter
    ports:
      - {{ $version->port }}:9000
    volumes:
      - {{ $home }}:/srv/app
    environment:
       - DB_HOST={{ $db_host }}
