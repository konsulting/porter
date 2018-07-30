  mysql:
    image: mysql:5.7
    ports:
      - 13306:3306
    environment:
      MYSQL_ROOT_PASSWORD: secret
    volumes:
      - ./storage/data/mysql:/var/lib/mysql
      - ./storage/log/mysql:/var/log/mysql
    networks:
      - porter
