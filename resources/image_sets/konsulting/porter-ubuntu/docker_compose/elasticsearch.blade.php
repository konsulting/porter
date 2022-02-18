  elasticsearch:
    image: {{ $imageSet->firstByServiceName('elasticsearch')->getName() }}
    ports:
      - 19200:9200
      - 19300:9300
    environment:
      - discovery.type=single-node
    volumes:
      - {{ $libraryPath }}/data/elasticsearch:/usr/share/elasticsearch/data
    networks:
      - porter
    restart: unless-stopped
