## Планируется, что тут будет CRUD API на PHP

Посмотреть, что получается, можно [тут](http://php-crud-api.alwaysdata.net/).

Пока реализована ознакомительная 0.0.1 версия CRUD API сущности 'book':

#### POST /books/ - CREATE

  <pre>
  BODY - JSON {
                  "title": title,
                  "author": author,
                  "published_at": year
              }
  </pre>

#### GET /books/ - READ

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

#### DELETE /books/ - DELETE

  <pre>
  BODY - JSON {
                  "id": id
              }
  </pre>

Все поля являются обязательными.
