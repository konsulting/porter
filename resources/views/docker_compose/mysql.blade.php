  mysql:
    image: mysql:5.7
    ports:
      - 13306:3306
    environment:
      MYSQL_ROOT_PASSWORD: secret
    volumes:
      - ./storage/data/mysql:/var/lib/mysql
      - ./storage/logs/mysql:/var/log/mysql
      - ./storage/config/mysql/history:/root/.mysql_history
    environment:
      - MYSQL_HISTFILE=/root/.mysql_history/history
    networks:
      - porter
