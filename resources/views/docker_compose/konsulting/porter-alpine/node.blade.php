  node:
    build:
      context: ./docker/{{ $imageSet }}
      dockerfile: node/Dockerfile
      cache_from:
        - {{ $imageSet }}-node:latest
    image: {{ $imageSet }}-node
    user: node
    volumes:
      - {{ $home }}:/srv/app
      - ./storage/config/user/ssh:/root/.ssh
      - ./storage/config/node/bash_history:/home/node/.bash_history
    networks:
      - porter
