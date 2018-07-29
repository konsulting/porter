  redis:
    image: redis:alpine
    volumes:
      - ./storage/data/redis:/data
    ports:
      - 16379:6379
    networks:
      - porter
