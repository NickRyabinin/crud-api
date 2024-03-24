<?php

namespace App\Tests;

use App\Core\Exceptions\InvalidDataException;
use App\Core\Exceptions\InvalidTokenException;

class OpinionTest extends BaseModelTestSetUp
{
    public function testStore(): void
    {
        $token = parent::makeDefaultBook();
        $parentId = 1;
        $opinionData = ['opinion' => 'New Opinion'];
        $result = $this->opinion->store($parentId, $token, $opinionData);

        $this->assertTrue($result);

        $childId = 1;
        $query = ("SELECT * FROM opinions WHERE book_id = :book_id AND opinion_id = :opinion_id");
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':book_id' => $parentId, ':opinion_id' => $childId]);
        $insertedOpinion = $stmt->fetch();

        $this->assertEquals($opinionData['opinion'], $insertedOpinion['opinion']);
    }

    public function testStoreWithInvalidData(): void
    {
        $parentId = 1;
        $invalidData = ['comment' => 'New Opinion'];
        $token = parent::makeDefaultBook();

        $this->expectException(InvalidDataException::class);

        $this->opinion->store($parentId, $token, $invalidData);
    }

    public function testStoreWithInvalidToken(): void
    {
        $parentId = 1;
        $opinionData = ['opinion' => 'New Opinion'];

        $this->expectException(InvalidTokenException::class);

        $this->opinion->store($parentId, 'invalid_token', $opinionData);
    }

    public function testIndex(): void
    {
        $bookId = 1;
        $data = [
            [
                'id' => 1,
                'author_login' => 'User 1',
                'book_id' => 1,
                'opinion_id' => 1,
                'opinion' => 'Opinion 1',
                'created_at' => '2024-01-01'
            ],
            [
                'id' => 2,
                'author_login' => 'User 2',
                'book_id' => 1,
                'opinion_id' => 2,
                'opinion' => 'Opinion 2',
                'created_at' => '2024-02-02'
            ]
        ];
        foreach ($data as $opinion) {
            $this->pdo->exec(
                "INSERT INTO opinions (author_login, book_id, opinion_id, opinion, created_at)
                VALUES (
                    '{$opinion['author_login']}', '{$opinion['book_id']}', '{$opinion['opinion_id']}',
                    '{$opinion['opinion']}', '{$opinion['created_at']}'
            )"
            );
        }
        $result = $this->opinion->index($bookId);

        $this->assertEquals($data, $result);
    }

    public function testShow(): void
    {
        $bookId = 1;
        $opinionId = 1;
        $opinionData = [
            'id' => 1,
            'author_login' => 'Default User',
            'book_id' => 1,
            'opinion_id' => 1,
            'opinion' => 'New Opinion',
            'created_at' => '2024-01-01'
        ];
        $this->pdo->exec(
            "INSERT INTO opinions (author_login, book_id, opinion_id, opinion, created_at)
            VALUES (
                '{$opinionData['author_login']}', '{$opinionData['book_id']}', '{$opinionData['opinion_id']}',
                '{$opinionData['opinion']}', '{$opinionData['created_at']}'
            )"
        );
        $result = $this->opinion->show($bookId, $opinionId);

        $this->assertEquals($opinionData, $result);
    }

    public function testUpdate(): void
    {
        $token = parent::makeDefaultOpinion();
        $parentId = 1;
        $childId = 1;
        $opinionData = ['opinion' => 'Updated Opinion'];
        $result = $this->opinion->update($parentId, $childId, $token, $opinionData);

        $this->assertTrue($result);
    }

    public function testUpdateWithInvalidData(): void
    {
        $token = parent::makeDefaultOpinion();
        $parentId = 1;
        $childId = 1;
        $opinionData = ['comment' => 'Updated Opinion'];

        $this->expectException(InvalidDataException::class);

        $this->opinion->update($parentId, $childId, $token, $opinionData);
    }

    public function testDestroy(): void
    {
        $token = parent::makeDefaultOpinion();
        $parentId = 1;
        $childId = 1;
        $result = $this->opinion->destroy($parentId, $childId, $token);

        $this->assertTrue($result);
    }
}
