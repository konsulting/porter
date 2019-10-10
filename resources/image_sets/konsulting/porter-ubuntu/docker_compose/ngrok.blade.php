  ngrok:
    image: {{ $imageSet->firstByServiceName('ngrok')->getName() }}
    ports:
      - 4040:4040
    networks:
      - porter

