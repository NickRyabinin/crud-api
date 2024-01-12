<?php

namespace books;

class Book
{
    private $properties = ['id', 'title', 'author', 'published_at'];
    private $id;
    private $title;
    private $author;
    private $publishedAt;
    private $pdo;

    public function __construct(PDO $pdo, $id = null)
    {
        $this->pdo = $pdo;
        $this->id = $id;

        if ($this->id) {
            $this->loadFromDb();
        }
    }

    public function loadFromDb()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM books WHERE id = :id');
        $stmt->execute([':id' => $this->id]);
        $book = $stmt->fetch();

        $this->title = $book['title'];
        $this->author = $book['author'];
        $this->publishedAt = $book['published_at'];
    }

    public function saveToDb()
    {
        if ($this->id) {
            $stmt = $this->pdo->prepare('
                UPDATE books SET title = :title, author = :author, published_at = :published_at WHERE id = :id
                ');
            $stmt->execute([
                ':title' => $this->title,
                ':author' => $this->author,
                ':published_at' => $this->publishedAt,
                ':id' => $this->id
            ]);
        } else {
            $stmt = $this->pdo->prepare('
                INSERT INTO books (title, author, published_at) VALUES (:title, :author, :published_at)
                ');
            $stmt->execute([
                ':title' => $this->title,
                ':author' => $this->author,
                ':published_at' => $this->publishedAt
            ]);

            $this->id = $this->pdo->lastInsertId();
        }
    }

    public function deleteFromDb()
    {
        $stmt = $this->pdo->prepare('DELETE FROM books WHERE id = :id');
        $stmt->execute([':id' => $this->id]);
    }

    // Геттеры и сеттеры для полей
    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
    }
}
