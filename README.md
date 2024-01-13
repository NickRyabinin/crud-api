## CRUD API на  чистом PHP

Посмотреть, что получается, можно [тут](http://php-crud-api.alwaysdata.net/).

Пока реализована MVP версия CRUD API сущности 'book':

#### POST /books/ - CREATE

  <pre>
  BODY - JSON {
                  "title": title,
                  "author": author,
                  "published_at": year
              }
  </pre>

Все поля являются обязательными.

#### GET /books/ - READ ALL

  No BODY needed

#### GET /books/*{id}* - READ single id

  No BODY needed

#### PUT /books/ - UPDATE

  <pre>
  BODY - JSON {
                  "id": id,
                  "title": title,
                  "author": author,
                  "published_at": year
              }
  </pre>

Все поля являются обязательными.

#### PATCH /books/ - UPDATE partial

  <pre>
  BODY - JSON {
                  "id": id,
                  "title": title,
                  "author": author,
                  "published_at": year
              }
  </pre>

Поле "id" является обязательным.
Остальные поля - должно присутствовать минимум одно.

#### DELETE /books/*{id}* - DELETE

  No BODY needed

Параметр "id" является обязательным.