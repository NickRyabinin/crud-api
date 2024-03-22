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

    public function testDestroy(): void
    {
        $token = parent::makeDefaultOpinion();
        $parentId = 1;
        $childId = 1;
        $result = $this->opinion->destroy($parentId, $childId, $token);

        $this->assertTrue($result);
    }
}
