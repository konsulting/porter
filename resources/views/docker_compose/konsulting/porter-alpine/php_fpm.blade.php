  php_fpm_{{ $version->safe }}:
    build:
      context: ./docker/{{ $imageSet }}
      dockerfile: php_fpm_{{ $version->safe }}/Dockerfile
      cache_from:
        - {{ $imageSet }}-php_fpm_{{ $version->safe }}:latest
    image: {{ $imageSet }}-php_fpm_{{ $version->safe }}
    networks:
      - porter
    ports:
      - 95{{ $version->short_form }}:9001
    volumes:
      - {{ $home }}:/srv/app:cached
      - ./storage/config/php_fpm_{{ $version->safe }}/php.ini:/etc/php{{ $version->major }}/php.ini
      - ./storage/config/php_fpm_{{ $version->safe }}/xdebug.ini:/etc/php{{ $version->major }}/conf.d/xdebug.ini
    environment:
      - HOST_MACHINE_NAME={{ $host_machine_name }}
      - RUNNING_ON_PORTER=true
    restart: unless-stopped
