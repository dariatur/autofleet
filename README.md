# Auto Fleet

## Installation and Setup
To quickly launch the project you will need docker and ddev. You can run the project without ddev, but then you'll need to configure the environment yourself.
**Note:** The repository includes a .env file with settings for connecting to containers inside ddev. In a real project, of course, this should not be done.

### Installation
1. Install ddev by following the [official documentation](https://ddev.com/get-started/).
2. Clone the repository:
```bash  
git clone https://github.com/maxmishyn/autofleet
```
3. Navigate to the project directory and start ddev:
```bash  
cd autofleet 
```
4. Start ddev:
```bash
ddev start
```
Note that when starting ddev, it automatically performs all subsequent operations from step 5 through step 9.

5. Install dependencies using Composer:
```bash
ddev composer install
```
6. Run database migrations:
```bash
ddev artisan migrate
```
7. Install resources for API documentation rendering:
```bash
ddev artisan api-platform:install
```

8. Run tests to make sure everything works:
```bash
ddev artisan test
```
9. Frontend compilation:
```bash
npm install
npm run build
```
10. Run e2e tests
```bash
npm run test:e2e
```

At this stage, the project should be ready to work.

You can open it in your browser at `https://plctest.ddev.site`

API documentation is available at `https://plctest.ddev.site/api/docs`

## API Implementation
### Main Entities
#### Car
An entity representing a car in the system. It has the following fields:
- **id**: Unique car identifier. The chosen type - ULID allows using it in URLs just like UUID without fear of identifier guessing. But unlike UUID, ULID preserves sorting by creation time, which reduces the load on the database index.
- **make**: Car make. In real projects, this field and the model field would likely be foreign keys referencing other tables to avoid data duplication and ensure flexibility when adding new makes and models.
- **model**: Car model.
- **year**: Car production year. For the test task, range restrictions from 2000 to 2025 are used.
- **price**: Car price. Uses `integer` type. Validator checks that the price is not less than 100.

## API Methods
#### Get list of cars
GET /api/cars
- Returns a list of all cars in the system.
- Content is paginated. Default page size is 30 cars. Current page is determined by the `page` parameter.
- Total number of cars is returned in the `X-Total-Count` header to support pagination. Page size is specified in the `X-Page-Count` header.

#### Get car information
GET /api/cars/{id}
- Returns car information by its unique identifier.

#### Create new car
POST /api/cars
- Creates a new car in the system.
- Request body must contain JSON with `make`, `model`, `year` and `price` fields.

#### Update car information
PUT /api/cars/{id}
- Updates car information by its unique identifier.
- Request body must contain JSON with `make`, `model`, `year` and `price` fields.

- _Note: this method's test is disabled because I accidentally found that there's a bug in the Laravel implementation of API Platform that prevents updating indexed fields. (https://github.com/api-platform/core/issues/7182).
Nevertheless, PUT is not used in SPA, so this doesn't limit functionality in any way._

#### Partial update of car information
PATCH /api/cars/{id}
- Partially updates car information by its unique identifier.
- Request body may contain JSON with any of the fields `make`, `model`, `year` and `price`.
- If a field is not specified, it will not be changed.

#### Delete car
DELETE /api/cars/{id}
- Deletes a car from the system by its unique identifier.

In case of validation errors or other errors, the API returns HTTP status 422 and an error message in JSON format.

## Optimization
As an example of caching for the GET /api/cars query, result caching in Redis using Laravel tools has been implemented. See implementation in app/Providers/CarDataProvider.php
It should be noted that the example uses a maximally simplified system for generating cached object identifiers. Actually, it's tied simply to the page number field for sorting.

## Automated Tests
The project contains a set of automated tests that check the main API functions. Tests cover the following aspects:
- CRUD operations for cars including invalid data handling
- testing cache for car selection and its invalidation
- unit tests for Car

----------------------------------------------------

# Auto Fleet

## Установка и настройка
Проект для быстрого запуска вам понадобится docker и ddev. Можно запустить проект без ddev, но тогда вам придется самостоятельно настраивать окружение.
**Примечание** В репозитарий включен файл .env с настройками для подключения к контейнерам внутри ddev. В реальном проекте, само собой, так делать не стоит. 

### Установка
1. Установите ddev, следуя [официальной документации](https://ddev.com/get-started/).
2. Клонируйте репозиторий:
```bash  
git clone https://github.com/maxmishyn/autofleet
```
3. Перейдите в директорию проекта и запустите ddev:
```bash  
cd autofleet 
```
4. Запустите ddev:
```bash
ddev start
```
Обратите внимание, что при запуске ddev автоматически все последующие операции с пункта 5 по поункт 9.

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
9. Компиляция фронтенда:
```bash
npm install
npm run build
```
10. Запустить e2e тесты
```bash
npm run test:e2e
```

На этом этапе проект должен быть готов к работе. 

Вы можете открыть его в браузере по адресу `https://plctest.ddev.site`

Документация API доступна по адресу `https://plctest.ddev.site/api/docs`

## Реализация API
### Основные сущности
#### Автомобиль (Car)
Сущность, представляющая автомобиль в системе. Имеет следующие поля:
- **id**: Уникальный идентификатор автомобиля. Выбранный тип - ULID позволяет использовать его в URL-ах также как и UUID не опосаясь риска подбора идентификатора. Но в отличие от UUID, ULID сохраняет сортировку по времени создания, что снижает нагрузку на индекс базы данных.
- **make**: Марка автомобиля. В реальных проектах это поле и поле model скорее всего будет внешним ключом ссылающимся на другие таблицы, чтобы избежать дублирования данных и обеспечить гибкость при добавлении новых марок и моделей.
- **model**: Модель автомобиля.
- **year**: Год выпуска автомобиля. Для тестового задания используются ограничения диапазона от 2000 до 2025 года.
- **price**: Цена автомобиля. Используется тип `integer`. Валидатор проверяет, что цена не меньше 100.

## Методы API
#### Получение списка автомобилей
GET /api/cars
- Возвращает список всех автомобилей в системе.
- Содержимое разбивается на страницы. Размер страницы по умолчанию - 30 автомобилей. Текущая страница определяется параметром `page`.
- Полное количество автомобилей возвращается в заголовке `X-Total-Count` для поддержки постраничного вывода. Размер страницы указан в заголовке `X-Page-Count`.

#### Получение информации об автомобиле
GET /api/cars/{id}
- Возвращает информацию об автомобиле по его уникальному идентификатору.

#### Создание нового автомобиля
POST /api/cars
- Создает новый автомобиль в системе.
- Тело запроса должно содержать JSON с полями `make`, `model`, `year` и `price`.

#### Обновление информации об автомобиле
PUT /api/cars/{id}
- Обновляет информацию об автомобиле по его уникальному идентификатору.
- Тело запроса должно содержать JSON с полями `make`, `model`, `year` и `price`.

- _Примечание: тест этого метода отключен, т.к. случайно нашел, что в Laravel реализации API Platform есть баг, который не дает обновлять интексированные поля. (https://github.com/api-platform/core/issues/7182).
Тем не мении PUT не используется в SPA, поэтому это никак не ограничивает функционал._


#### Частичное обновление информации об автомобиле
PATCH /api/cars/{id}
- Частично обновляет информацию об автомобиле по его уникальному идентификатору.
- Тело запроса может содержать JSON с любым из полей `make`, `model`, `year` и `price`.
- Если поле не указано, оно не будет изменено.

#### Удаление автомобиля
DELETE /api/cars/{id}
- Удаляет автомобиль из системы по его уникальному идентификатору.

При ошибке валидации или других ошибках API возвращает HTTP статус 422 код и сообщение об ошибке в формате JSON.

## Оптимизация
В качестве примера кеширования для выборки GET /api/cars внедрено кеширование результатов в Redis средствами Laravel. См. реализацию в app/Providers/CarDataProvider.php
Следует обратить внимание, что в примере используется максимально упрощенная системв генерации идентификатора кешированного объекта. Фактически он завязан просто на номер страницы поле для сортировки.

## Автоматизированные тесты
Проект содержит набор автоматизированных тестов, которые проверяют основные функции API. Тесты покрвают следующие аспекты:
- CRUD операции для автомобилей включая обработку невалидных данных
- тестрование кеша выборки автомобилей и его инвалидации
- юнит тесты для Car

