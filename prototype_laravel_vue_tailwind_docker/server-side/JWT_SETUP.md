# JWT Refresh Token Setup Guide

## Что исправлено в refresh token логике

### 1. Правильная валидация типа токена

-   Добавлена проверка `typ === 'refresh'` в middleware
-   Refresh токены создаются с клеймом `typ: 'refresh'`
-   Access токены не могут использоваться для обновления

### 2. Корректные TTL для разных типов токенов

-   **Access token**: использует `JWT_TTL` (по умолчанию 60 минут)
-   **Refresh token**: использует `JWT_REFRESH_TTL` (по умолчанию 30 дней = 43200 минут)
-   Cookie refresh token: 30 дней (43200 минут)

### 3. Улучшенная обработка ошибок

-   `TokenExpiredException` - токен истек
-   `TokenInvalidException` - токен поврежден
-   `JWTException` - общие ошибки JWT

### 4. Middleware для валидации

-   `CheckRefreshToken` middleware проверяет refresh токен до контроллера
-   Валидация типа, срока действия и подписи
-   Добавляет payload в request для использования в контроллере

## Настройка переменных окружения

Добавьте в `.env`:

```env
# JWT Configuration
JWT_SECRET=your-super-secret-key-here
JWT_TTL=60                    # Access token TTL в минутах
JWT_REFRESH_TTL=43200        # Refresh token TTL в минутах (30 дней)
JWT_ALGO=HS256               # Алгоритм шифрования
```

## Генерация JWT секрета

```bash
php artisan tinker
echo Str::random(64);
```

## API Endpoints

### Публичные маршруты

-   `POST /api/register` - регистрация пользователя
-   `POST /api/login` - вход в систему

### Маршруты с refresh token

-   `POST /api/refresh` - обновление токенов (middleware: `check.refresh.token`)

### Защищенные маршруты (JWT auth)

-   `POST /api/logout` - выход из системы
-   `GET /api/me` - информация о текущем пользователе

## Пример использования

### 1. Логин

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}'
```

Ответ содержит:

-   `token` - access token для API запросов
-   `expires_in` - время жизни в секундах
-   `refresh_token` - в HttpOnly cookie

### 2. Обновление токена

```bash
curl -X POST http://localhost:8000/api/refresh \
  -H "Cookie: refresh_token=your_refresh_token_here"
```

### 3. Использование access token

```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer your_access_token_here"
```

## Безопасность

-   Refresh токены хранятся в HttpOnly cookies
-   Проверка типа токена (`typ: 'refresh'`)
-   Разные TTL для access и refresh токенов
-   Автоматическая ротация refresh токенов при каждом обновлении

## Тестирование

```bash
# Запуск тестов
php artisan test

# Создание тестовой базы
php artisan migrate:fresh --seed
```
