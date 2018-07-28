  php_cli_{{ $version }}:
    build:
      context: .
      dockerfile: docker/php_cli_{{ $version }}/Dockerfile
    networks:
      - porter
    volumes:
      - {{ $path }}:/srv/app
    environment:
       - DB_HOST={{ $db_host }}
