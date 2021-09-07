# Advert API

API для хранения рекламных объявлений.
API состоит из трёх методов: создания, редактирования и открутки
Описание  протокола https://github.com/cmtt-ru/vacancy-backend/blob/main/TASK.md

## Запуск приложения
### Локально
Для локального запуска нужна запущенная база MongoDB
Данные для подключения к базе указываются через переменные окружения, по умолчанию используется адрес mongodb://localhost:27017
``` shell
$ export ADS_MONGO_URL='http://localhost:27017'
$ export ADS_MONGO_DATABASE='test'
$ composer install
$ /usr/bin/php8.0 -S localhost:8000 -t /public
```

### С помощью Docker
``` shell
$ docker-compose up
```

### Запуск функциональных тестов
Автотесты используют тестовую базу данных с именем autotest (указывается в phpunit.xml) и не могут быть запущены без этого файла конфигурации.
```shell
$ php vendor/phpunit/phpunit/phpunit --configuration phpunit.xml
```

## Описание протокола
### Метод создания объявления /ads
Добавляет новое объявление.

#### Входные параметры
| Поле | Описание  | Тип  |
|---|---|---|
| text | Заголовок объявления | строка |
| price | Стоимость одного показа | вещественное число |
| limit | Лимит показов | целое число |
| banner | Ссылка на картинку | url |

#### Пример запроса
```http request
POST /ads HTTP/1.1
Host: example.url
Content-Type: application/x-www-form-urlencoded
Content-Length: 68

text=PersistableAd2&price=100&limit=3&banner=https://linktoimage.png
```

### Метод редактирования объявления /ads/{id}
Находит объявление по id переданному в адресе и обновляет данные объявления

#### Входные параметры
| Поле | Описание  | Тип  |
|---|---|---|
| text | Заголовок объявления | строка |
| price | Стоимость одного показа | вещественное число |
| limit | Лимит показов | целое число |
| banner | Ссылка на картинку | url |

#### Пример запроса
```http request
POST /ads/1 HTTP/1.1
Host: example.url
Content-Type: application/x-www-form-urlencoded
Content-Length: 68

text=PersistableAd2&price=100&limit=3&banner=https://linktoimage.png
```

### Метод получения объявления для показа /ads/{id}
Возвращает данные наиболее подходящего для показа объявления у которого лимит показов не исчерпан.
При наличии нескольких объявлений будет выбрано одно по следующим критериям
* Объявление не было показно в прошлый раз
* Будет выбрано объяление с наибольшей ценой
* При одинаковой цене будет выбрано то что не показывалось дольше других

#### Пример запроса
```http request
GET /ads/relevant HTTP/1.1
Host: example.url
```

### Выходные параметры
Для всех методов формат ответа одинаковый

| Поле | Описание  | Тип  | 
|---|---|---|
| message | Статус ответа | строка | 
| code | Код ответа | целое число |
| data.id | Идентификатор созданного объявления | целое число |
| data.text | Заголовок объявления | строка |
| data.banner | Ссылка на картинку | url |

В случае успешной обработки запроса в поле `code` будет значение `200` и поле `message` значение `ОК`
В случае ошибки в `message` будет текст с описанием ошибки, а в поле `code` код ошибки, поле `data` будет пустым.

Возможные ошибки

| code | message  | метод  | описание
|---|---|---|---|
| 400 | There are nothing to show |  /ads/relevant  | Не создано ни одного объявления, либо у всех исчерпан лимит показов
| 400 | Invalid content in field "banner" |  /ads<br />/ads/{id}  | поле banner строка должна быть ссылкой
| 400 | Invalid content in field "price" |  /ads<br />/ads/{id}  | price должно быть рациональным числом
| 400 | Invalid content in field "limit" |  /ads<br />/ads/{id}  | limit должно быть целым числом
| 400 | Text field cannot be empty |  /ads<br />/ads/{id}  | Поле text не может быть пустым
| 400 | Not found object with id = {id} |  /ads/{id}  | Объявление с заданным идентификатором не найдено
| 500 | Invalid content in field "{fieldName}" |  /ads<br />/ads/{id}<br />/ads/relevant  | Внутренняя ошибка сервера



### Пример ответа
##### Успешный ответ
```http request
Host: example.url
Content-Type: application/json

{
    "message": "OK",
    "code": 200,
    "data": {
        "id": 1,
        "text": "PersistableAd2",
        "banner": "https://linktoimage.png"
    }
}
```

##### Ошибка
```http request
Host: example.url
Content-Type: application/json

{
    "message": "Not found object with id = 44",
    "code": 400,
    "data": []
}
```
