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
      - {{ $home }}:/srv/app
      - ./storage/config/php_fpm_{{ $version->safe }}/php.ini:/etc/php{{ $version->safe[0] }}/php.ini
      - ./storage/config/php_fpm_{{ $version->safe }}/xdebug.ini:/etc/php{{ $version->safe[0] }}/conf.d/xdebug.ini
    environment:
      - HOST_MACHINE_NAME={{ $host_machine_name }}
      - RUNNING_ON_PORTER=true
    restart: unless-stopped
