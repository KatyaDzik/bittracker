FROM nginx:1.17-alpine

ARG BASE_USER
ARG BASE_PASSWORD

# Настройка базовой авторизации
RUN apk add --no-cache apache2-utils
RUN htpasswd -c -b /etc/nginx/.htpasswd $BASE_USER $BASE_PASSWORD

# Копируем конфигурационный файл Nginx
COPY /docker/nginx/nginx.conf /etc/nginx/conf.d/default.con