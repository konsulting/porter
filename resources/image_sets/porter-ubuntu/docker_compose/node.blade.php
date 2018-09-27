  node:
    build:
      context: {{ $imageSetPath }}
      dockerfile: node/Dockerfile
      cache_from:
        - {{ $imageSet }}-node:latest
    image: {{ $imageSet }}-node
    user: node
    volumes:
      - {{ $home }}:/srv/app:delegated
      - {{ $libraryPath }}/config/user/ssh:/root/.ssh
      - {{ $libraryPath }}/config/node/bash_history:/home/node/.bash_history
    networks:
      - porter
