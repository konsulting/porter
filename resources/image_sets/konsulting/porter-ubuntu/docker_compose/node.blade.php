  node:
    build:
      context: {{ $imageSet->getDockerContext() }}
      dockerfile: node/Dockerfile
    image: {{ $imageSet->firstByServiceName('node')->getName() }}
    user: node
    volumes:
      - {{ $home }}:/srv/app
      - {{ $libraryPath }}/config/user/ssh:/root/.ssh
      - {{ $libraryPath }}/config/node/bash_history:/home/node/.bash_history
    networks:
      - porter
    ports:
      - 3000:3000
      - 3001:3001
      - 8080:8080
