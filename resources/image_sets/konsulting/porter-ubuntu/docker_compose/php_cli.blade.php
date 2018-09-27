  php_cli_{{ $version->safe }}:
    build:
      context: {{ $dockerContext }}
      dockerfile: php_cli_{{ $version->safe }}/Dockerfile
      cache_from:
        - {{ $imageSet }}-php_cli_{{ $version->safe }}:latest
    image: {{ $imageSet }}-php_cli_{{ $version->safe }}
    networks:
      - porter
    volumes:
      - {{ $home }}:/srv/app:delegated
      - {{ $libraryPath }}/composer/cache:/root/.composer/cache
      - {{ $libraryPath }}/config/user/ssh:/root/.ssh
      - {{ $libraryPath }}/config/{{ $version->cli_name }}/php.ini:/etc/php/{{ $version->version_number }}/cli/php.ini
      - {{ $libraryPath }}/config/{{ $version->cli_name }}/xdebug.ini:/etc/php/{{ $version->version_number }}/cli/conf.d/xdebug.ini
      - {{ $libraryPath }}/config/{{ $version->cli_name }}/bash_history:/root/.bash_history
    environment:
      - HOST_MACHINE_NAME={{ $host_machine_name }}
      - RUNNING_ON_PORTER=true
