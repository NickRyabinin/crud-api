<?php

namespace App\Tests;

use App\Controllers\OpinionController;
use App\Models\Opinion;
use App\Core\Exceptions\InvalidIdException;
use App\Core\Exceptions\InvalidTokenException;
use App\Core\Exceptions\InvalidDataException;

class OpinionControllerTest extends BaseControllerTestSetUp
{
    private Opinion $opinion;
    private OpinionController $controller;
    private string $validParentId;
    private string $validChildId;
    private array $validData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper->method('getResource')->with('parent')->willReturn('book');
        $this->opinion = $this->createMock(Opinion::class);
        $this->opinion->method('__toString')->willReturn('opinion');
        $this->controller = new OpinionController($this->opinion, $this->view, $this->helper);
        $this->validParentId = (string)random_int(1, 10);
        $this->validChildId = (string)random_int(1, 10);
        $this->validData = ['opinion' => 'Test Opinion'];
    }

    public function testCreate(): void
    {
        $this->setupTest($this->validParentId, 'validToken', $this->validData, '');
        $this->opinion->expects($this->once())->method('store')
            ->with($this->validParentId, 'validToken', $this->validData)
            ->willReturn(true);
        $this->view->expects($this->once())->method('send')
            ->with('201', ['message' => "Done, opinion added successfully"]);
        $this->controller->create();
    }

    public function testCreateWithInvalidToken(): void
    {
        $this->setupTest($this->validParentId, 'invalidToken', $this->validData, '');
        $this->opinion->expects($this->once())->method('store')
            ->with($this->validParentId, 'invalidToken', $this->validData)
            ->willThrowException(new InvalidTokenException());
        $this->view->expects($this->once())->method('send')
            ->with('401', ['error' => 'Unauthorized, no such token']);
        $this->controller->create();
    }

    public function testCreateWithInvalidData(): void
    {
        $data = [
            'comment' => 'Test Opinion'
        ];
        $this->setupTest($this->validParentId, 'validToken', $data, '');
        $this->opinion->expects($this->once())->method('store')
            ->with($this->validParentId, 'validToken', $data)
            ->willThrowException(new InvalidDataException());
        $this->view->expects($this->once())->method('send')
            ->with('400', ['error' => 'Invalid input data']);
        $this->controller->create();
    }

    public function testCreateWithInvalidParentId(): void
    {
        $this->setupTest('invalidParentId', 'validToken', $this->validData, '');
        $this->opinion->expects($this->once())->method('store')
            ->with('invalidParentId', 'validToken', $this->validData)
            ->willThrowException(new InvalidIdException());
        $this->view->expects($this->once())->method('send')
            ->with('404', ['error' => 'Resource not found']);
        $this->controller->create();
    }

    public function testReadIndex(): void
    {
        $id1 = rand(1, 100);
        $date1 = date('YYYY-MM-DD HH:MM:SS', strtotime('-1 day'));
        $id2 = rand(1, 100);
        $date2 = date('YYYY-MM-DD HH:MM:SS');
        $data = [
            [
                'id' => $id1,
                'author_login' => 'Incognito Anonymous',
                'book_id' => $this->validParentId,
                'opinion_id' => $this->validChildId,
                'created_at' => $date1
            ],
            [
                'id' => $id2,
                'author_login' => 'Someone',
                'book_id' => $this->validParentId,
                'opinion_id' => $this->validChildId + 1,
                'created_at' => $date2
            ]
        ];
        $this->setupTest($this->validParentId);
        $this->opinion->expects($this->once())->method('index')->willReturn($data);
        $this->view->expects($this->once())->method('send')->with('200', $data);
        $this->controller->read();
    }

    public function testReadWithInvalidChildId(): void
    {
        $this->setupTest($this->validParentId, childId: false);
        $this->view->expects($this->once())->method('send')
            ->with('400', ['error' => 'Invalid ID']);
        $this->controller->read();
    }

    public function testReadIndexEmpty(): void
    {
        $this->setupTest($this->validParentId);
        $this->opinion->expects($this->once())->method('index')->willReturn([]);
        $this->view->expects($this->once())->method('send')
            ->with('404', ['error' => 'No records']);
        $this->controller->read();
    }

    public function testReadShow(): void
    {
        $id = rand(1, 100);
        $date = date('YYYY-MM-DD HH:MM:SS');
        $data = [
            [
                'id' => $id,
                'author_login' => 'Incognito Anonymous',
                'book_id' => $this->validParentId,
                'opinion_id' => $this->validChildId,
                'created_at' => $date
            ]
        ];
        $this->setupTest($this->validParentId, childId: $this->validChildId);
        $this->opinion->expects($this->once())->method('show')->willReturn($data);
        $this->view->expects($this->once())->method('send')->with('200', $data);
        $this->controller->read();
    }

    public function testReadShowEmpty(): void
    {
        $this->setupTest($this->validParentId, childId: 'unexistedChildId');
        $this->opinion->expects($this->once())->method('show')->willReturn(false);
        $this->view->expects($this->once())->method('send')
            ->with('404', ['error' => 'No record with such ID']);
        $this->controller->read();
    }

    public function testUpdate(): void
    {
        $this->setupTest($this->validParentId, 'validToken', $this->validData, $this->validChildId);
        $this->opinion->expects($this->once())->method('update')
            ->with($this->validParentId, $this->validChildId, 'validToken', $this->validData)
            ->willReturn(true);
        $this->view->expects($this->once())->method('send')
            ->with('200', ['message' => "Done, opinion updated successfully"]);
        $this->controller->update();
    }

    public function testUpdateWithInvalidToken(): void
    {
        $this->setupTest($this->validParentId, 'invalidToken', $this->validData, $this->validChildId);
        $this->opinion->expects($this->once())->method('update')
            ->with($this->validParentId, $this->validChildId, 'invalidToken', $this->validData)
            ->willThrowException(new InvalidTokenException());
        $this->view->expects($this->once())->method('send')
            ->with('401', ['error' => 'Unauthorized, no such token']);
        $this->controller->update();
    }

    public function testUpdateWithInvalidParentId(): void
    {
        $this->setupTest('invalidParentId', 'validToken', $this->validData, $this->validChildId);
        $this->opinion->expects($this->once())->method('update')
            ->with('invalidParentId', $this->validChildId, 'validToken')
            ->willThrowException(new InvalidIdException());
        $this->view->expects($this->once())->method('send')
            ->with('404', ['error' => 'Resource not found']);
        $this->controller->update();
    }

    public function testUpdateWithInvalidChildId(): void
    {
        $this->setupTest($this->validParentId, 'validToken', $this->validData, false);
        $this->view->expects($this->once())->method('send')
            ->with('400', ['error' => 'Invalid ID']);
        $this->controller->update();
    }

    public function testUpdateWithInvalidData(): void
    {
        $data = [
            'comment' => 'Test Opinion'
        ];
        $this->setupTest($this->validParentId, 'validToken', $data, $this->validChildId);
        $this->opinion->expects($this->once())->method('update')
            ->with($this->validParentId, $this->validChildId, 'validToken', $data)
            ->willThrowException(new InvalidDataException());
        $this->view->expects($this->once())->method('send')
            ->with('400', ['error' => 'Invalid input data']);
        $this->controller->update();
    }

    public function testDelete(): void
    {
        $this->setupTest($this->validParentId, 'validToken', childId: $this->validChildId);
        $this->opinion->expects($this->once())->method('destroy')
            ->with($this->validParentId, $this->validChildId, 'validToken')
            ->willReturn(true);
        $this->view->expects($this->once())->method('send')
            ->with('200', ['message' => "Done, opinion deleted successfully"]);
        $this->controller->delete();
    }

    public function testDeleteWithInvalidToken(): void
    {
        $this->setupTest($this->validParentId, 'invalidToken', childId: $this->validChildId);
        $this->opinion->expects($this->once())->method('destroy')
            ->with($this->validParentId, $this->validChildId, 'invalidToken')
            ->willThrowException(new InvalidTokenException());
        $this->view->expects($this->once())->method('send')
            ->with('401', ['error' => 'Unauthorized, no such token']);
        $this->controller->delete();
    }

    public function testDeleteWithInvalidParentId(): void
    {
        $this->setupTest('invalidParentId', 'validToken', childId: $this->validChildId);
        $this->opinion->expects($this->once())->method('destroy')
            ->with('invalidParentId', $this->validChildId, 'validToken')
            ->willThrowException(new InvalidIdException());
        $this->view->expects($this->once())->method('send')
            ->with('404', ['error' => 'No record with such ID']);
        $this->controller->delete();
    }

    public function testDeleteWithInvalidChildId(): void
    {
        $this->setupTest($this->validParentId, 'validToken', childId: false);
        $this->view->expects($this->once())->method('send')
            ->with('400', ['error' => 'Invalid ID']);
        $this->controller->delete();
    }

    public function testDeleteWithPDOException(): void
    {
        $this->setupTest($this->validParentId, 'validToken', [], $this->validChildId);
        $this->opinion->expects($this->once())->method('destroy')
            ->with($this->validParentId, $this->validChildId, 'validToken')
            ->willThrowException(new InvalidDataException());
        $this->view->expects($this->once())->method('send')
            ->with('400', ['error' => 'Invalid input data']);
        $this->controller->delete();
    }
}
