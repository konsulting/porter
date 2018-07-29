  node:
    build:
      context: .
      dockerfile: docker/node/Dockerfile
    image: konsulting/porter-node
    user: node
    volumes:
      - {{ $home }}:/srv/app
    networks:
      - porter
