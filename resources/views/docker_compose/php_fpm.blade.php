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
      - ./storage/config/php_fpm_{{ $version->safe }}/php.ini:/usr/local/etc/php/php.ini
    environment:
       - DB_HOST={{ $db_host }}
