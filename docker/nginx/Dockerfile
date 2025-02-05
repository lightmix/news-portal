FROM nginx:latest

RUN apt-get update && \
    apt-get clean && \
    rm -r /var/lib/apt/lists/* && \
    mkdir /etc/nginx/ssl && \
    openssl req -x509 -newkey rsa:4096 -nodes -keyout /etc/nginx/ssl/server.key -out /etc/nginx/ssl/server.crt -days 365 -subj '/CN=*' && \
    mkdir -m 700 -p /srv/nginx/cache && \
    chown nginx: -R /srv/nginx

COPY nginx.conf /etc/nginx/nginx.conf
COPY default.conf /etc/nginx/conf.d/default.conf
