  node:
    build:
      context: {{ $imageSet->getDockerContext() }}
      dockerfile: node/Dockerfile
    image: {{ $imageSet->firstByServiceName('node')->getName() }}
    user: node
    volumes:
      - {{ $home }}:/srv/app:delegated
      - {{ $libraryPath }}/config/user/ssh:/root/.ssh
      - {{ $libraryPath }}/config/node/bash_history:/home/node/.bash_history
    networks:
      - porter
