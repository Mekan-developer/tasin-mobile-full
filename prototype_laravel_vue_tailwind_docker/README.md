# Laravel + Vue 3 + MySQL Docker Project

Этот проект представляет собой **Laravel 11 backend** и **Vue 3 frontend**, подключённые к **MySQL 8**, и развёрнутые с помощью **Docker Compose**.

## 📁 Структура проекта

---

## ⚙️ Требования

- Docker ≥ 20.x
- Docker Compose ≥ 2.x
- Node.js ≥ 18 (для локальной разработки Vue)
- Composer (для локальной разработки Laravel)

---

## 🚀 Запуск проекта

1. Склонировать репозиторий:

```bash
git clone https://github.com/username/project.git
cd project
```

2. Создать .env для Laravel:
   cp server-side/.env.example server-side/.env

3. Проверить и настроить .env:
   DB_CONNECTION=mysql
   DB_HOST=mysql_db
   DB_PORT=3306 # внутренний порт MySQL в Docker
   DB_DATABASE=prototype
   DB_USERNAME=myuser
   DB_PASSWORD=secret

4. Запустить Docker Compose:
   docker-compose up -d --build

5. Проверить статус контейнеров:
   docker-compose ps

🔹 Миграции и сиды
docker exec -it laravel_app php artisan migrate --force
docker exec -it laravel_app php artisan db:seed --force

📦 Сборка Vue
cd client-side
npm install
npm run build
