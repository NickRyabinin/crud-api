#!/bin/bash

# Скрипт для установки переменных окружения для запуска docker-compose

echo "Введите URL для подключения к базе данных (DATABASE_URL):"
read DATABASE_URL

echo "Введите пароль для root пользователя в MySQL (MYSQL_ROOT_PASSWORD):"
read MYSQL_ROOT_PASSWORD

echo "Введите название базы данных (MYSQL_DATABASE):"
read MYSQL_DATABASE

echo "Введите имя пользователя для доступа к базе данных (MYSQL_USER):"
read MYSQL_USER

echo "Введите пароль для пользователя доступа к базе данных (MYSQL_PASSWORD):"
read MYSQL_PASSWORD

# Установка переменных окружения
export DATABASE_URL=$DATABASE_URL
export MYSQL_ROOT_PASSWORD=$MYSQL_ROOT_PASSWORD
export MYSQL_DATABASE=$MYSQL_DATABASE
export MYSQL_USER=$MYSQL_USER
export MYSQL_PASSWORD=$MYSQL_PASSWORD

echo "Переменные окружения успешно установлены."

# Запуск docker-compose
docker-compose up -d