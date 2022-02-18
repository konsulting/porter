  meilisearch:
    image: {{ $imageSet->firstByServiceName('meilisearch')->getName() }}
    ports:
      - 17700:7700
    volumes:
      - {{ $libraryPath }}/data/meilisearch:/data.ms
    networks:
      - porter
    restart: unless-stopped
