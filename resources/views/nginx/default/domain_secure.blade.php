server {
    listen 80;
    server_name {{ $site }} www.{{ $site }} *.{{ $site }};
    return 302 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name {{ $site }} www.{{ $site }} *.{{ $site }};

    root /srv/app/{{ $name }}/public;

    index index.php index.html;

    server_tokens off;

    charset utf-8;
    client_max_body_size 128M;

    fastcgi_buffers 8 16k;
    fastcgi_buffer_size 32k;

    ssl_certificate /etc/ssl/{{ $site }}.crt;
    ssl_certificate_key /etc/ssl/{{ $site }}.key;

    error_log /proc/self/fd/2;
    access_log /proc/self/fd/2;

    location = /favicon.ico { log_not_found off; access_log off; }
    location = /robots.txt  { log_not_found off; access_log off; }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_index index.php;
        fastcgi_pass php_fpm_{{ $version }}:9000;
    }

    location ~ /\.ht {
        deny all;
    }
}
