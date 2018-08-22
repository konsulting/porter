  php_fpm_{{ $version->safe }}:
    build:
      context: ./docker/{{ $imageSet }}
      dockerfile: php_fpm_{{ $version->safe }}/Dockerfile
      cache_from:
        - {{ $imageSet }}-php_fpm_{{ $version->safe }}:latest
    image: {{ $imageSet }}-php_fpm_{{ $version->safe }}
    networks:
      - porter
    volumes:
      - {{ $home }}:/srv/app:delegated
      - ./storage/config/{{ $version->fpm_name }}/php.ini:/etc/php/{{ $version->version_number }}/fpm/php.ini
      - ./storage/config/{{ $version->fpm_name }}/xdebug.ini:/etc/php/{{ $version->version_number }}/fpm/conf.d/xdebug.ini
    environment:
      - HOST_MACHINE_NAME={{ $host_machine_name }}
      - RUNNING_ON_PORTER=true
    restart: unless-stopped
