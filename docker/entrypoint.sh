#!/bin/sh
set -e

# Substitute environment variables in Nginx config
envsubst '$PORT' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default.conf.tmp
mv /etc/nginx/conf.d/default.conf.tmp /etc/nginx/conf.d/default.conf

php-fpm &
nginx -g "daemon off;"