# Отправка заявок с сайта в AmoCRM

Это страница с простенькой формой для отправки данных на AmoCRM.

## Начало работы

1. Склонируйте репозиторий:
   ```bash
   git clone https://github.com/Lukerazor32/Sending-requests-to-AmoCRM.git
   ```
2. Настройте API для вашего аккаунта:

    Откройте файл [ config/amocrm.php ](https://github.com/Lukerazor32/Sending-requests-to-AmoCRM/blob/main/config/amocrm.php)

    Заполните его данными из вашего аккаунта AmoCRM.
## Запуск сервера

Для запуска сервера достаточно выполнить команду:
```bash
   docker-compose up --build
```
Во время запуска происходит миграция таблицы ```tokens``` для работы с авторизацией.
После успешного запуска контейнеров, страница будет доступна по адресу:
http://localhost:81/src/views/form.php

Для работы используется официальный репозиторий [AmoCRM API PHP](https://github.com/amocrm/amocrm-api-php.git)
