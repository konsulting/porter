server {
    listen 80;
    server_name {{ $site }} www.{{ $site}} *.{{ $site }};

    root /srv/app/{{ $name }}/public;

    index index.php index.html;

    server_tokens off;

    charset utf-8;
    client_max_body_size 128M;

    fastcgi_buffers 8 16k;
    fastcgi_buffer_size 32k;

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
