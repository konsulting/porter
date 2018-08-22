  php_cli_{{ $version->safe }}:
    build:
      context: ./docker/{{ $imageSet }}
      dockerfile: php_cli_{{ $version->safe }}/Dockerfile
      cache_from:
        - {{ $imageSet }}-php_cli_{{ $version->safe }}:latest
    image: {{ $imageSet }}-php_cli_{{ $version->safe }}
    networks:
      - porter
    ports:
      - 96{{ $version->short_form }}:9001
    volumes:
      - {{ $home }}:/srv/app:cached
      - ./storage/config/user/ssh:/root/.ssh
      - ./storage/config/php_cli_{{ $version->safe }}/php.ini:/etc/php{{ $version->major }}/php.ini
      - ./storage/config/php_cli_{{ $version->safe }}/xdebug.ini:/etc/php{{ $version->major }}/conf.d/xdebug.ini
      - ./storage/config/php_cli_{{ $version->safe }}/bash_history:/root/.bash_history
    environment:
      - HOST_MACHINE_NAME={{ $host_machine_name }}
      - RUNNING_ON_PORTER=true
