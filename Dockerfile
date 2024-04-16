# Используем официальный образ PHP с Apache
FROM php:8.1-apache

# Устанавливаем необходимую библиотеку 'oniguruma'
RUN apt-get update && apt-get install -y libonig-dev

# Устанавливаем расширения PHP, которые могут понадобиться
RUN docker-php-ext-install pdo pdo_mysql mbstring

# Настраиваем Apache для использования mod_rewrite
RUN a2enmod rewrite

# Перезапускаем сервис Apache2 для применения изменений
RUN service apache2 restart

# Копируем конфигурацию Apache для настройки единой точки входа
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Копируем исходный код приложения в контейнер
COPY project/. /var/www/html

# Устанавливаем права доступа для директории с исходным кодом
RUN chown -R www-data:www-data /var/www/html

# Открываем порт 80 для доступа к веб-серверу
EXPOSE 80