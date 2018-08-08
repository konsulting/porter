  php_cli_{{ $version->safe }}:
    build:
      context: ./docker/{{ $imageSet }}
      dockerfile: php_cli_{{ $version->safe }}/Dockerfile
      cache_from:
        - {{ $imageSet }}-php_cli_{{ $version->safe }}:latest
    image: {{ $imageSet }}-php_cli_{{ $version->safe }}
    networks:
      - porter
    volumes:
      - {{ $home }}:/srv/app
      - ./storage/config/php_cli_{{ $version->safe }}/php.ini:/etc/php/{{ $version->version_number }}/cli/php.ini
      - ./storage/config/php_cli_{{ $version->safe }}/xdebug.ini:/etc/php/{{ $version->version_number }}/cli/conf.d/xdebug.ini
      - ./storage/config/php_cli_{{ $version->safe }}/bash_history:/root/.bash_history
    environment:
      - HOST_MACHINE_NAME={{ $host_machine_name }}
      - RUNNING_ON_PORTER=true
