
docker container stop atom-mysql
docker container rm atom-mysql

docker container stop atom-gearmand
docker container rm atom-gearmand

docker container stop memcached
docker container rm memcached

docker container stop atom-elasticsearch
docker container rm atom-elasticsearch

docker container stop atom-nginx
docker container rm atom-nginx

docker container stop atom-worker
docker container rm atom-worker

docker container stop atom
docker container rm atom

docker network rm atom-network

docker volume rm atom-mysql
docker volume rm atom-elasticsearch
docker volume rm atom-src
docker volume rm atom-data
docker volume rm atom-composer-deps
