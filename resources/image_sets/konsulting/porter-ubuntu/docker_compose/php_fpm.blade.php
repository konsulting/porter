  php_fpm_{{ $version->safe }}:
    build:
      context: {{ $imageSet->getDockerContext() }}
      dockerfile: php_cli_{{ $version->safe }}/Dockerfile
    image: {{ $imageSet->firstByServiceName('php_fpm_'.$version->safe)->getName() }}
    networks:
      - porter
    volumes:
      - {{ $home }}:/srv/app:delegated
      - {{ $libraryPath }}/config/{{ $version->fpm_name }}/php.ini:/etc/php/{{ $version->version_number }}/fpm/php.ini
      - {{ $libraryPath }}/config/{{ $version->fpm_name }}/xdebug.ini:/etc/php/{{ $version->version_number }}/fpm/conf.d/xdebug.ini
    environment:
      - HOST_MACHINE_NAME={{ $host_machine_name }}
      - RUNNING_ON_PORTER=true
    restart: unless-stopped
