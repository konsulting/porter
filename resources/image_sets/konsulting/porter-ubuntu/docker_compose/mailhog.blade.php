  mailhog:
    image: {{ $imageSet->firstByServiceName('mailhog')->getName() }}
    networks:
      - porter
    ports:
      - 1025:1025
      - 8025:8025
    restart: unless-stopped
