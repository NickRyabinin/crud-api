## CRUD API на  чистом PHP

Попытка в REST-like CRUD API по паттерну MVC в парадигме ООП :-)
Пока реализован MVP для сущности 'book'.
Посмотреть, что получается, можно [тут](http://php-crud-api.alwaysdata.net/).

#### POST /books/ - CREATE

  <pre>
  BODY - JSON {
                  "title": title,
                  "author": author,
                  "published_at": year
              }
  </pre>

Все поля в BODY являются обязательными.

#### GET /books/ - READ ALL

  No BODY needed

#### GET /books/*{id}* - READ single id

  No BODY needed

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

  No BODY needed

Параметр "id" является обязательным.