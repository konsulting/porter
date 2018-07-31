  php_fpm_{{ $version->safe }}:
    build:
      context: .
      dockerfile: docker/php_fpm_{{ $version->safe }}/Dockerfile
      cache_from:
        - konsulting/porter-php_fpm_{{ $version->safe }}:latest
    image: konsulting/porter-php_fpm_{{ $version->safe }}
    networks:
      - porter
    ports:
      - {{ $version->port }}:9000
    volumes:
      - {{ $home }}:/srv/app
      - ./storage/config/php_fpm_{{ $version->safe }}/php.ini:/usr/local/etc/php/php.ini
      - ./storage/config/php_fpm_{{ $version->safe }}/xdebug.ini:/usr/local/etc/php/conf.d/xdebug.ini
    environment:
      - HOST_MACHINE_NAME={{ $host_machine_name }}
      - RUNNING_ON_PORTER=true
