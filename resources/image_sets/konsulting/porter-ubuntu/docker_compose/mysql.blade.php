  mysql:
    image: {{ $imageSet->firstByServiceName('mysql')->getName() }}
    ports:
      - 13306:3306
    environment:
      - MYSQL_ROOT_PASSWORD=secret
      - MYSQL_HISTFILE=/root/.mysql_history/history
    volumes:
      - {{ $libraryPath }}/data/mysql:/var/lib/mysql
      - {{ $libraryPath }}/config/mysql/history:/root/.mysql_history
      - {{ $libraryPath }}/config/mysql/mysql.cnf:/etc/mysql/conf.d/mysql.cnf
    networks:
      - porter
    restart: unless-stopped
