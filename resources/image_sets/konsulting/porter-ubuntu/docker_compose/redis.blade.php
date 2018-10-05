  redis:
    image: {{ $imageSet->firstByServiceName('redis')->getName() }}
    volumes:
      - {{ $libraryPath }}/data/redis:/data
    ports:
      - 16379:6379
    networks:
      - porter
    restart: unless-stopped
