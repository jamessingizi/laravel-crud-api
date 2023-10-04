#!/bin/bash

# Add required configs
env | grep APP_ | tee /usr/share/nginx/html/.env 1>/dev/null

# Update nginx to match worker_processes to no. of cpu's
procs=$(cat /proc/cpuinfo | grep processor | wc -l)
sed -i -e "s/worker_processes  1/worker_processes $procs/" /etc/nginx/nginx.conf

# cache dir
mkdir -p /usr/share/nginx/html/data/cache

# Start supervisord and services
echo "running supervisord..."
/usr/local/bin/supervisord -n -c /etc/supervisord.conf
