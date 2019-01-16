  php_cli_{{ $version->safe }}:
    build:
      context: {{ $imageSet->getDockerContext() }}
      dockerfile: php_cli_{{ $version->safe }}/Dockerfile
    image: {{ $imageSet->firstByServiceName('php_cli_'.$version->safe)->getName() }}
    networks:
      - porter
    volumes:
      - {{ $home }}:/srv/app:delegated
      - {{ $libraryPath }}/composer:/root/.composer
      - {{ $libraryPath }}/config/user/ssh:/root/.ssh
      - {{ $libraryPath }}/config/{{ $version->cli_name }}/php.ini:/etc/php/{{ $version->version_number }}/cli/php.ini
      - {{ $libraryPath }}/config/{{ $version->cli_name }}/xdebug.ini:/etc/php/{{ $version->version_number }}/cli/conf.d/xdebug.ini
      - {{ $libraryPath }}/config/{{ $version->cli_name }}/bash_history:/root/.bash_history
    environment:
      - HOST_MACHINE_NAME={{ $host_machine_name }}
      - RUNNING_ON_PORTER=true
