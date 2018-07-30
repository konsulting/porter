  node:
    build:
      context: .
      dockerfile: docker/node/Dockerfile
      cache_from:
        - konsulting/porter-node:latest
    image: konsulting/porter-node
    user: node
    volumes:
      - {{ $home }}:/srv/app
    networks:
      - porter
