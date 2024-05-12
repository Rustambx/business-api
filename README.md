### Для работы сайта необходимо следующие команды:
<b>1.</b>Эта команда запускает все контейнеры.
<br>
```
docker-compose up -d
```
```
composer install
```
```
// Создать env файл и прописать доступы к mysql
DB_CONNECTION=mysql
DB_HOST=business-test-mysql
DB_PORT=3306
DB_DATABASE=business-test
DB_USERNAME=root
DB_PASSWORD=root
```

<b>2.</b>Доп. команды для запуска проекта.
<br>
```
// Зайдем в основной контейнер
docker exec -it business-test-php bash
```
```
// Миграции
php artisan migrate
```
```
// Запуск тестов
php artisan test
```


### Документация для REST API:
**1. Регистрация пользователя**<br>
**Url:** /api/register
<br>**Request-type:** POST
<br>**Body:**

|Param    |Type    |Description|
|---------|--------|-----------|
|name|String|Имя|
|email|String|Email|
|password|String|Пароль|

<br>**Response:**
```json 
{
    "message": "User registered successfully"
}
```

**2. Логин пользователя**<br>
**Url:** /api/login
<br>**Request-type:** POST
<br>**Body:**

|Param    |Type    |Description|
|---------|--------|-----------|
|email|String|Email|
|password|String|Пароль|

<br>**Response:**
```json 
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzE1NDY2NTc2LCJleHAiOjE3MTU0NzAxNzYsIm5iZiI6MTcxNTQ2NjU3NiwianRpIjoiWXAyTEtlaURVeUVKY3p5byIsInN1YiI6IjEyIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.KmPiFZZgvQqZNmmSXDvWiRr74Hy1pBfqDfDQAbrIF88",
    "token_type": "bearer",
    "expires_in": 3600
}
```

**3. О себе**<br>
**Url:** /api/me
<br>**Request-type:** POST
<br>**Header:** authorization = bearer access_token
<br>**Response:**
```json 
{
    "id": 1,
    "name": "Name",
    "email": "Email",
    "email_verified_at": null,
    "created_at": "Дата создания",
    "updated_at": "Дата редактирования"
}
```

**4. Выйти**<br>
**Url:** /api/logout
<br>**Request-type:** POST
<br>**Header:** authorization = bearer access_token
<br>**Response:**
```json 
{
    "message": "Successfully logged out"
}
```

**5. Добавление заметки**<br>
**Url:** /api/notes
<br>**Request-type:** POST
<br>**Header:** authorization = bearer access_token
<br>**Body:**

|Param    |Type    |Description|
|---------|--------|-----------|
|title|String|Заголовок|
|content|String|Контент|

<br>**Response:**
```json 
{
    "title": "Заголовок",
    "content": "Контент",
    "user_id": "ID пользователя",
    "created_at": "Дата создания",
    "updated_at": "Дата редактирования"
    "id": 10
}
```

**6. Все заметки пользователя**<br>
**Url:** /api/notes
<br>**Request-type:** GET
<br>**Header:** authorization = bearer access_token
<br>**Response:**
```json 
{
    "id": "ID Заметки",
    "title": "Заголовок",
    "content": "Контент",
    "user_id": "ID пользователя",
    "created_at": "Дата создания",
    "updated_at": "Дата редактирования"
    "id": 10
}
```

**7. Заметка пользователя**<br>
**Url:** /api/notes/{id}
<br>**Request-type:** GET
<br>**Header:** authorization = bearer access_token
<br>**Response:**
```json 
{
    "id": "ID Заметки",
    "title": "Заголовок",
    "content": "Контент",
    "user_id": "ID пользователя",
    "created_at": "Дата создания",
    "updated_at": "Дата редактирования"
    "id": 10
}
```

**8. Редактирование заметки**<br>
**Url:** /api/notes/update/{id}
<br>**Request-type:** POST
<br>**Header:** authorization = bearer access_token
<br>**Body:**

|Param    |Type    |Description|
|---------|--------|-----------|
|title|String|Заголовок|
|content|String|Контент|

<br>**Response:**
```json 
{
    "title": "Заголовок",
    "content": "Контент",
    "user_id": "ID пользователя",
    "created_at": "Дата создания",
    "updated_at": "Дата редактирования"
    "id": 10
}
```

**9. Удаление заметки пользователя**<br>
**Url:** /api/notes/{id}
<br>**Request-type:** DELETE
<br>**Header:** authorization = bearer access_token
<br>**Response:**
```json 
{
    "message": "Note deleted"
}
```
