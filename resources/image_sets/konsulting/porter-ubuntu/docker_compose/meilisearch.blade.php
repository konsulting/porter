  meilisearch:
    image: {{ $imageSet->firstByServiceName('meilisearch')->getName() }}
    ports:
      - 17700:7700
    volumes:
      - {{ $libraryPath }}/data/meilisearch:/data.ms
    environment:
      - MEILI_MASTER_KEY=masterKey
    networks:
      - porter
    restart: unless-stopped
