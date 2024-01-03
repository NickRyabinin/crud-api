## Планируется, что тут будет CRUD API на PHP

Посмотреть, что получается, можно [тут](http://php-crud-api.alwaysdata.net/).

Пока реализована ознакомительная 0.0.1 версия CRUD API сущности 'book':

### POST / - CREATE

  <pre>
  BODY - JSON {
                  "title": title,
                  "author": author,
                  "published_at": year
              }
  </pre>

### GET / - READ

  No BODY needed

### PUT / - UPDATE

  <pre>
  BODY - JSON {
                  "id": id,
                  "title": title,
                  "author": author,
                  "published_at": year
              }
  </pre>

### DELETE / - DELETE

  <pre>
  BODY - JSON {
                  "id": id
              }
  </pre>

Все поля являются обязательными.
