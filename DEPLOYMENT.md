# 🚀 Деплой Alem на Ubuntu Server (Production)

## 📋 Требования
- Ubuntu Server 22.04 LTS или новее
- Root или sudo доступ
- Доменное имя (опционально, но рекомендуется)

## 1️⃣ Подготовка сервера

### Обновление системы
```bash
sudo apt update && sudo apt upgrade -y
```

### Установка необходимых пакетов
```bash
sudo apt install -y software-properties-common curl git unzip supervisor nginx redis-server
```

### Установка PHP 8.2
```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common php8.2-mysql \
    php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath \
    php8.2-sqlite3 php8.2-redis php8.2-intl
```

### Установка Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### Установка Node.js 20.x (для npm)
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### Проверка установки
```bash
php -v
composer -V
node -v
npm -v
```

## 2️⃣ Настройка базы данных

### Установка PostgreSQL (рекомендуется для production)
```bash
sudo apt install -y postgresql postgresql-contrib
sudo systemctl start postgresql
sudo systemctl enable postgresql
```

### Создание БД и пользователя
```bash
sudo -u postgres psql

# В PostgreSQL консоли:
CREATE DATABASE alem_db;
CREATE USER alem_user WITH ENCRYPTED PASSWORD 'your_secure_password';
GRANT ALL PRIVILEGES ON DATABASE alem_db TO alem_user;
\q
```

**Или используйте MySQL:**
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation

sudo mysql
CREATE DATABASE alem_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'alem_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON alem_db.* TO 'alem_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 3️⃣ Клонирование и настройка проекта

### Создание директории для приложения
```bash
sudo mkdir -p /var/www/alem
sudo chown -R $USER:www-data /var/www/alem
cd /var/www/alem
```

### Клонирование репозитория
```bash
git clone https://github.com/aidosgal/alem.git .
# Или загрузите файлы через SFTP/SCP
```

### Установка зависимостей
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### Настройка .env файла
```bash
cp .env.example .env
nano .env
```

**Обновите следующие параметры:**
```env
APP_NAME=Alem
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=alem_db
DB_USERNAME=alem_user
DB_PASSWORD=your_secure_password

# Или MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=alem_db
# DB_USERNAME=alem_user
# DB_PASSWORD=your_secure_password

# Redis (для кеша и очередей)
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
BROADCAST_CONNECTION=reverb

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Reverb WebSocket
REVERB_APP_ID=your_app_id
REVERB_APP_KEY=your_app_key
REVERB_APP_SECRET=your_app_secret
REVERB_HOST=yourdomain.com
REVERB_PORT=443
REVERB_SCHEME=https

# Vite (для production)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Генерация ключа приложения
```bash
php artisan key:generate
```

### Выполнение миграций
```bash
php artisan migrate --force
```

### Создание символической ссылки для storage
```bash
php artisan storage:link
```

### Настройка прав доступа
```bash
sudo chown -R www-data:www-data /var/www/alem/storage
sudo chown -R www-data:www-data /var/www/alem/bootstrap/cache
sudo chmod -R 775 /var/www/alem/storage
sudo chmod -R 775 /var/www/alem/bootstrap/cache
```

### Оптимизация
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 4️⃣ Настройка Nginx

### Создание конфигурации сайта
```bash
sudo nano /etc/nginx/sites-available/alem
```

**Вставьте следующую конфигурацию:**
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/alem/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # WebSocket reverse proxy для Reverb
    location /app {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 86400;
    }
}
```

### Активация сайта
```bash
sudo ln -s /etc/nginx/sites-available/alem /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## 5️⃣ Настройка SSL (Let's Encrypt)

### Установка Certbot
```bash
sudo apt install -y certbot python3-certbot-nginx
```

### Получение SSL сертификата
```bash
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### Автоматическое обновление сертификата
```bash
sudo systemctl status certbot.timer
```

## 6️⃣ Настройка Supervisor (для очередей и Reverb)

### Создание конфигурации для Laravel Queue
```bash
sudo nano /etc/supervisor/conf.d/alem-worker.conf
```

**Содержимое:**
```ini
[program:alem-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/alem/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/alem/storage/logs/worker.log
stopwaitsecs=3600
```

### Создание конфигурации для Reverb WebSocket
```bash
sudo nano /etc/supervisor/conf.d/alem-reverb.conf
```

**Содержимое:**
```ini
[program:alem-reverb]
process_name=%(program_name)s
command=php /var/www/alem/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/alem/storage/logs/reverb.log
stopwaitsecs=3600
```

### Применение конфигурации
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
sudo supervisorctl status
```

## 7️⃣ Настройка планировщика Laravel

### Добавление в crontab
```bash
sudo crontab -e -u www-data
```

**Добавьте строку:**
```
* * * * * cd /var/www/alem && php artisan schedule:run >> /dev/null 2>&1
```

## 8️⃣ Настройка Firewall

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
sudo ufw status
```

## 9️⃣ Мониторинг и логи

### Просмотр логов Laravel
```bash
tail -f /var/www/alem/storage/logs/laravel.log
```

### Просмотр логов Nginx
```bash
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```

### Просмотр логов Supervisor
```bash
sudo tail -f /var/www/alem/storage/logs/worker.log
sudo tail -f /var/www/alem/storage/logs/reverb.log
```

### Мониторинг процессов
```bash
sudo supervisorctl status
```

## 🔄 Процесс обновления (после git push)

### Создайте скрипт деплоя
```bash
nano /var/www/alem/deploy.sh
```

**Содержимое:**
```bash
#!/bin/bash
set -e

echo "🚀 Starting deployment..."

cd /var/www/alem

# Включаем maintenance mode
php artisan down

# Получаем последние изменения
git pull origin master

# Обновляем зависимости
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Выполняем миграции
php artisan migrate --force

# Очищаем и пересоздаем кеш
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Перезапускаем queue workers
sudo supervisorctl restart all

# Выключаем maintenance mode
php artisan up

echo "✅ Deployment completed!"
```

### Сделайте скрипт исполняемым
```bash
chmod +x /var/www/alem/deploy.sh
```

### Запуск деплоя
```bash
./deploy.sh
```

## 🔧 Troubleshooting

### Проблема: 500 Internal Server Error
```bash
# Проверьте права доступа
sudo chown -R www-data:www-data /var/www/alem/storage
sudo chmod -R 775 /var/www/alem/storage

# Проверьте логи
tail -f /var/www/alem/storage/logs/laravel.log
```

### Проблема: WebSocket не подключается
```bash
# Проверьте Reverb
sudo supervisorctl status alem-reverb
sudo tail -f /var/www/alem/storage/logs/reverb.log

# Проверьте порт
sudo netstat -tlnp | grep 8080

# Перезапустите Reverb
sudo supervisorctl restart alem-reverb
```

### Проблема: Очереди не обрабатываются
```bash
# Проверьте worker
sudo supervisorctl status alem-worker
sudo tail -f /var/www/alem/storage/logs/worker.log

# Перезапустите workers
sudo supervisorctl restart alem-worker:*
```

### Очистка кеша после изменений
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 🔐 Безопасность

1. **Измените все пароли в .env**
2. **Настройте fail2ban:**
   ```bash
   sudo apt install fail2ban
   sudo systemctl enable fail2ban
   ```

3. **Регулярно обновляйте систему:**
   ```bash
   sudo apt update && sudo apt upgrade -y
   ```

4. **Настройте backup базы данных:**
   ```bash
   # Для PostgreSQL
   pg_dump -U alem_user alem_db > backup.sql
   
   # Для MySQL
   mysqldump -u alem_user -p alem_db > backup.sql
   ```

## 📊 Мониторинг производительности

### Установка monitoring tools
```bash
sudo apt install htop iotop nethogs
```

### Мониторинг Redis
```bash
redis-cli ping
redis-cli info stats
```

## ✅ Финальная проверка

После деплоя проверьте:
- [ ] Сайт открывается по HTTPS
- [ ] SSL сертификат валиден
- [ ] WebSocket подключается
- [ ] Можно отправлять сообщения в чате
- [ ] Файлы загружаются корректно
- [ ] Очереди обрабатываются
- [ ] Логи не показывают ошибок

## 🎉 Готово!

Ваше приложение Alem теперь запущено в production на Ubuntu Server!

**Полезные команды:**
```bash
# Перезапуск всех сервисов
sudo systemctl restart nginx php8.2-fpm redis-server
sudo supervisorctl restart all

# Проверка статуса
sudo systemctl status nginx
sudo supervisorctl status

# Просмотр всех логов
sudo tail -f /var/www/alem/storage/logs/*.log
```
