  browser:
    build:
      context: {{ $imageSet->getDockerContext() }}
      dockerfile: chromedriver/Dockerfile
    image: {{ $imageSet->firstByServiceName('chromedriver')->getName() }}
    networks:
      - porter
    environment:
      WHITELISTED_IPS: ""
    cap_add:
      - "SYS_ADMIN"
    restart: unless-stopped
