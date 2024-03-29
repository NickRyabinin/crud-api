[![tests](https://github.com/NickRyabinin/crud-api/actions/workflows/tests.yml/badge.svg)](https://github.com/NickRyabinin/crud-api/actions/workflows/tests.yml)
[![ftp-deploy](https://github.com/NickRyabinin/crud-api/actions/workflows/ftp-deploy.yml/badge.svg)](https://github.com/NickRyabinin/crud-api/actions/workflows/ftp-deploy.yml)
[![Maintainability](https://api.codeclimate.com/v1/badges/f66141ceb354dd3f56a5/maintainability)](https://codeclimate.com/github/NickRyabinin/crud-api/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/f66141ceb354dd3f56a5/test_coverage)](https://codeclimate.com/github/NickRyabinin/crud-api/test_coverage)

## CRUD API на  чистом PHP

Попытка в REST-like CRUD API по паттерну MVC в парадигме ООП :-)

Пока реализован pre-MVP для сущностей 'user', 'book' и сущности 'opinion', связанной с 'book' отношением many-to-one.

Посмотреть, что получается, можно [тут](http://php-crud-api.alwaysdata.net/).

### Эндпойнты для сущности 'user':

#### POST /users/ - CREATE

'Регистрация' пользователя - в ответ пользователь получает token, который требуется указывать в заголовке

  <pre>
  Authorization: Bearer {token}
  </pre>

при последующих обращениях к API (кроме GET запросов) для авторизации действий пользователя.

  <pre>
  BODY - JSON {
                  "login": login,
                  "email": email
              }
  </pre>

Все поля в BODY являются обязательными.


#### GET /users/ - READ


#### GET /users/*{id}* - READ *{id}*


#### PUT | PATCH /users/*{id}* - method not allowed


#### DELETE /users/ - DELETE

Пользователь может удалить только себя, id не нужен.

### Эндпойнты для сущности 'book':

#### POST /books/ - CREATE

  <pre>
  BODY - JSON {
                  "title": title,
                  "author": author,
                  "published_at": year
              }
  </pre>

Все поля в BODY являются обязательными.

#### GET /books/ - READ


#### GET /books/*{id}* - READ *{id}*


#### PUT | PATCH /books/*{id}* - UPDATE

  <pre>
  BODY - JSON {
                  "title": title,
                  "author": author,
                  "published_at": year
              }
  </pre>

Параметр "id" является обязательным.
В BODY должно присутствовать минимум одно поле.


#### DELETE /books/*{id}* - DELETE

Параметр "id" является обязательным.

### Эндпойнты для сущности 'opinion', связанной с сущностью 'book':

#### POST /books/*{book_id}*/opinions/ - CREATE

  <pre>
  BODY - JSON {
                  "opinion": opinion
              }
  </pre>

Поле "opinion" является обязательным.

#### GET /books/*{book_id}*/opinions/ - READ


#### GET /books/*{book_id}*/opinions/*{opinion_id}* - READ *{opinion_id}*


#### PUT | PATCH /books/*{book_id}*/opinions/*{opinion_id}* - UPDATE

  <pre>
  BODY - JSON {
                  "opinion": opinion
              }
  </pre>

Параметры "book_id" и "opinion_id" являются обязательными.
В BODY должно присутствовать минимум одно поле.


#### DELETE /books/*{book_id}*/opinions/*{opinion_id}* - DELETE

Параметры "book_id" и "opinion_id" являются обязательными.