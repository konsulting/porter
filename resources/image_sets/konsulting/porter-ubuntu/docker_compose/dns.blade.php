  dns:
    image: {{ $imageSet->firstByServiceName('dns')->getName() }}
    networks:
      - porter
    ports:
      - "53:53/tcp"
      - "53:53/udp"
    cap_add:
      - NET_ADMIN
    command: --log-facility=-
    volumes:
      - {{ $libraryPath }}/config/dnsmasq/dnsmasq.conf:/etc/dnsmasq.conf
      - {{ $libraryPath }}/config/dnsmasq/dnsmasq.d:/etc/dnsmasq.d
    restart: unless-stopped
