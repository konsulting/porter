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
      - ./storage/config/user/ssh:/root/.ssh
      - ./storage/config/php_cli_{{ $version->safe }}/php.ini:/etc/php{{ $version->safe[0] }}/php.ini
      - ./storage/config/php_cli_{{ $version->safe }}/xdebug.ini:/etc/php{{ $version->safe[0] }}/conf.d/xdebug.ini
      - ./storage/config/php_cli_{{ $version->safe }}/bash_history:/root/.bash_history
    environment:
      - HOST_MACHINE_NAME={{ $host_machine_name }}
      - RUNNING_ON_PORTER=true
