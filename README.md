# Alem - Платформа для управления заказами и чата

## 🚀 Быстрый старт (Development)

### Требования
- PHP 8.2+
- Composer
- Node.js 20+
- SQLite (или PostgreSQL/MySQL)

### Установка

```bash
# 1. Клонирование репозитория
git clone https://github.com/aidosgal/alem.git
cd alem

# 2. Установка зависимостей
composer install
npm install

# 3. Настройка окружения
cp .env.example .env
php artisan key:generate

# 4. Создание базы данных
touch database/database.sqlite

# 5. Выполнение миграций
php artisan migrate

# 6. Создание символической ссылки для storage
php artisan storage:link

# 7. Компиляция assets
npm run build

# 8. Запуск серверов (в разных терминалах)
# Терминал 1: Laravel
php artisan serve

# Терминал 2: Reverb WebSocket
php artisan reverb:start

# Терминал 3 (опционально): Vite для hot reload
npm run dev
```

## 🧪 Тестирование чата

### 1. Создайте тестовые данные
```bash
php artisan db:seed
```

### 2. Откройте тестовую страницу
Перейдите на `http://localhost:8000/test-chat.html`

### 3. Войдите как менеджер
- URL: `http://localhost:8000/manager/login`
- Зарегистрируйте нового менеджера
- Создайте организацию
- Создайте статусы заказов (или инициализируйте стандартные)

### 4. Тестирование WebSocket
1. В браузере менеджера откройте раздел "Чат"
2. В тестовой странице введите Chat ID
3. Нажмите "Подключиться к WebSocket"
4. Отправляйте сообщения с обеих сторон!

## 📁 Структура проекта

```
alem/
├── app/
│   ├── Events/              # WebSocket события
│   ├── Http/Controllers/    # Контроллеры
│   ├── Models/              # Eloquent модели
│   ├── Repositories/        # Репозитории для работы с БД
│   └── Services/            # Бизнес-логика
├── database/
│   ├── migrations/          # Миграции БД
│   └── seeders/            # Сиды для тестовых данных
├── resources/
│   ├── js/                 # JavaScript (Laravel Echo)
│   └── views/              # Blade шаблоны
├── routes/
│   ├── web.php            # Web маршруты
│   ├── api.php            # API маршруты
│   └── channels.php       # Broadcasting каналы
└── public/
    └── test-chat.html     # Тестовая страница чата
```

## 🎯 Основные функции

### Для менеджеров
- ✅ Аутентификация и управление организациями
- ✅ CRUD вакансий
- ✅ CRUD услуг
- ✅ Канбан доска заказов с кастомными статусами
- ✅ Real-time чат с аппликантами
- ✅ Отправка файлов и изображений
- ✅ Создание заказов прямо из чата
- ✅ WebSocket с автореконнектом

### Технологии
- **Backend:** Laravel 11, SQLite/PostgreSQL
- **Frontend:** Tailwind CSS, Vanilla JavaScript
- **Real-time:** Laravel Reverb (WebSocket)
- **Broadcasting:** Laravel Echo + Pusher Protocol
- **Fonts:** JetBrains Mono

## 🔌 API Endpoints

### Чат (Manager)
```
GET    /manager/chat                    - Список чатов
GET    /manager/chat/{id}               - Открыть чат
POST   /manager/chat/{id}/message       - Отправить сообщение
GET    /manager/chat/{id}/messages      - Загрузить историю
POST   /manager/chat/{id}/order         - Создать заказ из чата
GET    /manager/chat/message/{id}/download - Скачать файл
```

### Orders
```
GET    /manager/orders                  - Канбан доска
PATCH  /manager/orders/{id}/status      - Обновить статус
```

### Order Statuses
```
GET    /manager/order-statuses          - Список статусов
POST   /manager/order-statuses          - Создать статус
PUT    /manager/order-statuses/{id}     - Обновить статус
DELETE /manager/order-statuses/{id}     - Удалить статус
POST   /manager/order-statuses/initialize - Создать стандартные
```

## 🔧 Конфигурация

### .env ключевые параметры

```env
# WebSocket
BROADCAST_CONNECTION=reverb
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# База данных (SQLite для разработки)
DB_CONNECTION=sqlite

# Кеш и очереди
CACHE_STORE=database
QUEUE_CONNECTION=database
```

## 🐛 Troubleshooting

### WebSocket не подключается
```bash
# Проверьте что Reverb запущен
php artisan reverb:start

# Проверьте порт
lsof -i :8080
```

### Ошибки прав доступа
```bash
chmod -R 775 storage bootstrap/cache
```

### Очистка кеша
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 📚 Дополнительно

### Запуск всего одной командой
```bash
# Установка и запуск
composer setup

# Разработка (с hot reload)
composer dev
```

### Полезные команды
```bash
# Просмотр логов
tail -f storage/logs/laravel.log

# Проверка роутов
php artisan route:list

# Проверка миграций
php artisan migrate:status
```

## 🚢 Production

Смотрите [DEPLOYMENT.md](DEPLOYMENT.md) для инструкций по деплою на Ubuntu Server.

## 📝 License

MIT

