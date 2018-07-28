  php_cli_{{ $version->safe }}:
    build:
      context: .
      dockerfile: docker/php_cli_{{ $version->safe }}/Dockerfile
    networks:
      - porter
    volumes:
      - {{ $home }}:/srv/app
    environment:
       - DB_HOST={{ $db_host }}
