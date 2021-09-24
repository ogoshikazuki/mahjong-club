FROM phppm/nginx:2.0.3-2

ENV DEBIAN_FRONTEND noninteractive

RUN apk add tzdata && cp /usr/share/zoneinfo/Asia/Tokyo /etc/localtime \
    && echo "Asia/Tokyo" >  /etc/timezone

RUN apk upgrade --update && apk --no-cache add git libzip-dev zip unzip autoconf gcc g++ make \
    freetype-dev libjpeg libjpeg-turbo-dev libpng-dev pcre-dev \
    && apk add --no-cache --virtual .build-deps \
    make \
    gcc \
    g++ \
    && apk del .build-deps

WORKDIR /var/www

CMD ["--bootstrap=laravel", "--app-env=prod"]
