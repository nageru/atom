#!/bin/bash
if [ $# != 16 ]; then
   echo "Incorrect parameters"
   echo "Usage: $0 --mariadb_ip <ip> --mariadb_port <port>"
   exit
fi

while [[ "$#" -gt 0 ]]; do
    case $1 in
        --mariadb_ip) MARIADB_SERVER_IP="$2"; shift ;;
        --mariadb_port) MARIADB_PORT="$2"; shift ;;
        *) echo "Unknown parameter passed: $1"; exit 1 ;;
    esac
    shift
done

echo "MARIADB_SERVER_IP=$MARIADB_SERVER_IP"
echo "MARIADB_PORT=$MARIADB_PORT"
echo "Return to continue"
read p

HOME=/home/nageru/atom
MARIADB_DATA=/mnt/data/mysql
MARIADB_CFG=/mnt/data/mysql/conf
NGINX_CFG=/mnt/data/nginx
ELASTICSEARCH_DATA=/mnt/data/elasticseach
ATOM_SRC=/mnt/data/atom/src
ATOM_DATA=/mnt/data/atom-data
ATOM_COMPOSER_DEPS=/mnt/data/atom/src/vendor/composer

echo -n "Creating network..."
docker network create -d bridge atom-network
echo done

docker volume create --opt type=none --opt o=bind --opt device=$MARIADB_DATA atom-mysql
docker volume create --opt type=none --opt o=bind --opt device=$ELASTICSEARCH_DATA atom-elasticsearch
docker volume create --opt type=none --opt o=bind --opt device=$ATOM_SRC atom-src
docker volume create --opt type=none --opt o=bind --opt device=$ATOM_DATA atom-data
docker volume create --opt type=none --opt o=bind --opt device=$ATOM_COMPOSER_DEPS atom-composer-deps



echo -n "Creating atom database..."
docker run -d --env MYSQL_ROOT_PASSWORD="ca62d5332ed24d2f04198178b7e450cd" \
	--env MYSQL_DATABASE=atom \
	--env MYSQL_USER=atom \
	--env MYSQL_PASSWORD=AeJua4xe \
        --name atom-mysql \
	--user root \
	-p ${MARIADB_PORT}:3306/tcp \
	-e "TZ=Europe/Madrid" \
	--volume atom-mysql:/var/lib/mysql \
	--mount type=bind,source=${MARIADB_CFG}/etc/mysql/mysqld.cnf,target=/etc/my.cnf.d/mysqld.cnf readonly=true \
        --network atom-network \
	percona:8.0
echo done
echo "Return to continue"
read p
value=$(docker exec -t atom-mysql mysql --silent -h $MARIADB_SERVER_IP --port ${MARIADB_PORT} -uroot -pca62d5332ed24d2f04198178b7e450cd -e "SHOW DATABASES;" |grep -c ERROR)
while [ "$value" != "0" ]; 
do 
   echo -n "."; 
   sleep 5; 
   value=$(docker exec -t atom-mysql mysql --silent -h $MARIADB_SERVER_IP --port ${MARIADB_PORT} -uroot -pca62d5332ed24d2f04198178b7e450cd -e "SHOW DATABASES;" |grep -c ERROR)
done
echo done
echo "Return to continue"
read p

echo -n "Creating gearman..."
docker run -d -p 63005:4730/tcp --network arkhive-network \
	-e "TZ=Europe/Madrid" \
	--user gearman \
	--name atom-gearmand  \
	artefactual/gearmand
echo done
echo "Return to continue"
read p

echo -n "Creating memcached..."
docker run -d -p 63004:11211/tcp --network arkhive-network \
	-e "TZ=Europe/Madrid" \
	--name atom-memcached  \
	memcached -p 11211 -m 128 -u memcache
echo done
echo "Return to continue"
read p

echo -n "Creating elasticsearch..."
docker run -d -p 63002:9200/tcp\
        --env cluster.name=atom-cluster \
        --env node.name=atom-node \
        --name atom-elasticsearch\
	-e "TZ=Europe/Madrid" \
	--env network.host=0.0.0.0 \
        --env bootstrap.memory_lock=true \
        --env  "ES_JAVA_OPTS=-Xms640m -Xmx640m" \
        --env cluster.routing.allocation.disk.threshold_enabled=false \
        --env xpack.security.enabled=false \
        --ulimit memlock=-1:-1 \
	--volume elasticsearch-data:/usr/share/elasticsearch/data \
        -p 62002:9200/tcp \
	--network arkhive-network \
	docker.elastic.co/elasticsearch/elasticsearch:5.6.16
echo done
echo "Return to continue"
read p

echo -n "Creating nginx..."
docker run -d -p 63001:80/tcp --network arkhive-network \
	-e "TZ=Europe/Madrid" \
	--mount type=volume src=atom-src dest=/atom/src readonly=true\
	--mount type=bind,source=${NGINX_CFG}/etc/nginx/nginx.conf,target=/etc/nginx/nginx.conf readonly=true\
	--name atom-nginx \
        nginx:latest
echo done
echo "Return to continue"
read p

echo -n "Creating atom-worker..."
docker run -d --network arkhive-network \
	-e "TZ=Europe/Madrid" \
        --env ATOM_DEVELOPMENT_MODE=off \
        --env ATOM_ELASTICSEARCH_HOST=atom-elasticsearch \
        --env ATOM_MEMCACHED_HOST=atom-memcached \
        --env ATOM_GEARMAND_HOST=atom-gearmand \
        --env ATOM_MYSQL_DSN=mysql:host=atom-mysql;port=3306;dbname=atom;charset=utf8mb4 \
        --env ATOM_MYSQL_USERNAME=atom \
        --env ATOM_MYSQL_PASSWORD=AeJua4xe \ 
	--mount type=volume src=atom-src dest=/atom/src \ 
        --mount type=volume src=atom-composer-deps dest=/atom/src/vendor/composer \
	--name atom-worker \
	--link atom-mysql \
	--link atom-gearmand \
        nageru:atom:1.0 worker
echo done
echo "Return to continue"
read p

echo -n "Creating atom..."
docker run -d --network arkhive-network \
	-e "TZ=Europe/Madrid" \
        --env ATOM_DEVELOPMENT_MODE=off \
        --env ATOM_ELASTICSEARCH_HOST=atom-elasticsearch \
        --env ATOM_MEMCACHED_HOST=atom-memcached \
        --env ATOM_GEARMAND_HOST=atom-gearmand \
        --env ATOM_MYSQL_DSN=mysql:host=atom-mysql;port=3306;dbname=atom;charset=utf8mb4 \
        --env ATOM_MYSQL_USERNAME=atom \
        --env ATOM_MYSQL_PASSWORD=AeJua4xe \ 
	--mount type=volume src=atom-src dest=/atom/src \ 
        --mount type=volume src=atom-composer-deps dest=/atom/src/vendor/composer \
	--name atom \
	--link atom-mysql \
        nageru:atom:1.0
echo done



