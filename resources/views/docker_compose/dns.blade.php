  dns:
    image: andyshinn/dnsmasq
    networks:
      - porter
    ports:
      - "53:53/tcp"
      - "53:53/udp"
    cap_add:
      - NET_ADMIN
    command: --log-facility=-
    volumes:
      - ./storage/config/dnsmasq/dnsmasq.conf:/etc/dnsmasq.conf
      - ./storage/config/dnsmasq/dnsmasq.d:/etc/dnsmasq.d
    restart: unless-stopped
