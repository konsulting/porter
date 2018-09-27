  browser:
    build:
      context: {{ $dockerContext }}
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
