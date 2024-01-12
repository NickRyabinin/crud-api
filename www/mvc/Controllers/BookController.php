<?php

namespace books;

class BookController
{
    private $book;

    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    public function create($title, $author, $publishedAt)
    {
        $this->book->setTitle($title);
        $this->book->setAuthor($author);
        $this->book->setPublishedAt($publishedAt);
        $this->book->saveToDb();

        return $this->book;
    }

    public function read()
    {
        return $this->book->loadFromDb();
    }

    public function update($title, $author, $publishedAt)
    {
        $this->book->setTitle($title);
        $this->book->setAuthor($author);
        $this->book->setPublishedAt($publishedAt);
        $this->book->saveToDb();

        return $this->book;
    }

    public function delete()
    {
        $this->book->deleteFromDb();

        return $this->book;
    }
}
