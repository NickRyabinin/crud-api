# Используем официальный образ PHP с Apache
FROM php:8.1-apache

# Устанавливаем расширения PHP, которые могут понадобиться
RUN docker-php-ext-install pdo_mysql

# Копируем исходный код в контейнер
COPY . /var/www/html

# Устанавливаем права доступа для директории с исходным кодом
RUN chown -R www-data:www-data /var/www/html

# Определяем точку монтирования для внешних данных
VOLUME /var/www/html

# Открываем порт 80 для доступа к веб-серверу
EXPOSE 80