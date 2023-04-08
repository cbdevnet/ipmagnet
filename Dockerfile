FROM lscr.io/linuxserver/nginx:latest

ENV URL your-ipmagnet-url-or-IP-without-trailing-slash.com
ENV ENABLE_INTERVAL true
ENV INTERVAL 60

RUN apk add --update --upgrade --no-cache git

RUN git clone https://github.com/cbdevnet/ipmagnet.git /config/www
RUN sed -i "s|http://localhost:80/ipmagnet/|$URL/|g" /config/www/index.php
RUN sed -i "s|$trackerInterval=300;|$trackerInterval=$INTERVAL;|g" /config/www/index.php
RUN sed -i "s|$enableInterval=false;|$trackerInterval=$ENABLE_INTERVAL;|g" /config/www/index.php
RUN chown -R 911:911 /config/www
