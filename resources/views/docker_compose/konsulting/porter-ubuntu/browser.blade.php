  browser:
    build:
      context: ./docker/{{ $imageSet }}
      dockerfile: chromedriver/Dockerfile
      cache_from:
        - {{ $imageSet }}-chromedriver:latest
    image: {{ $imageSet }}-chromedriver
    networks:
      - porter
    environment:
      WHITELISTED_IPS: ""
    cap_add:
      - "SYS_ADMIN"
    restart: unless-stopped
