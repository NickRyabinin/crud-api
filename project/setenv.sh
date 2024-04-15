#!/bin/bash

# Скрипт для установки переменных окружения для запуска docker-compose

echo "Введите URL для подключения к базе данных (DATABASE_URL) вида 'pdoDBType://user:password@host:port/dbName' :"
read DATABASE_URL

# Разбираем переменную DATABASE_URL на составляющие
MYSQL_USER=$(echo $DATABASE_URL | awk -F'[/:@]' '{print $4}')
MYSQL_PASSWORD=$(echo $DATABASE_URL | awk -F'[/:@]' '{print $5}')
MYSQL_DATABASE=$(echo $DATABASE_URL | awk -F'[/:@]' '{print $8}')

# Установка переменных окружения
export DATABASE_URL=$DATABASE_URL
export MYSQL_ROOT_PASSWORD=$MYSQL_PASSWORD
export MYSQL_DATABASE=$MYSQL_DATABASE
export MYSQL_USER=$MYSQL_USER
export MYSQL_PASSWORD=$MYSQL_PASSWORD

echo "Переменные окружения успешно установлены."

# Запуск docker-compose
docker-compose up -d
