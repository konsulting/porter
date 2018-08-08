  mailhog:
    image: mailhog/mailhog:v1.0.0
    networks:
      - porter
    ports:
      - 1025:1025
      - 8025:8025
    restart: unless-stopped
