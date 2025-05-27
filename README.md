# Auto Fleet

## Установка и настройка
Проект для быстрого запуска вам понадобится docker и ddev. Можно запустить проект без ddev, но тогда вам придется самостоятельно настраивать окружение.

### Установка
1. Установите ddev, следуя [официальной документации](https://ddev.com/get-started/).
2. Клонируйте репозиторий:
```bash  
git clone https://github.com/maxmishyn/autofleetcommand
```
3. Перейдите в директорию проекта и запустите ddev:
```bash  
cd autofleetcommand 
```
4. Запустите ddev:
```bash
ddev start
```
5. Установите зависимости с помощью Composer:
```bash
ddev composer install
```
6. Запустите миграции базы данных:
```bash
ddev artisan migrate
```
7. Установите ресурсы для рендеринга API документации:
```bash
ddev artisan api-platform:install
```

8. "Прогоните" тесты, чтобы убедиться, что всё работает:
```bash
ddev artisan test
```
На этом этапе проект должен быть готов к работе. 

Вы можете открыть его в браузере по адресу `https://autofleetcommand.ddev.site`

Документация API доступна по адресу `https://autofleetcommand.ddev.site/api/docs`

