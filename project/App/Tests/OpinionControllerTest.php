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

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper->method('getResource')->with('parent')->willReturn('book');
        $this->opinion = $this->createMock(Opinion::class);
        $this->opinion->method('__toString')->willReturn('opinion');
        $this->controller = new OpinionController($this->opinion, $this->view, $this->helper);
        $this->validParentId = (string)random_int(1, 10);
        $this->validChildId = (string)random_int(1, 10);
    }

    public function testCreate(): void
    {
        // $this->opinion->belongsTo = ['book'];
        $data = [
            'opinion' => 'Test Opinion'
        ];
        $this->setupTest($this->validParentId, 'validToken', $data, '');
        $this->opinion->expects($this->once())->method('store')
            ->with($this->validParentId, 'validToken', $data)
            ->willReturn(true);
        $this->view->expects($this->once())->method('send')
            ->with('201', ['message' => "Done, opinion added successfully"]);
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

    public function testUpdate(): void
    {
        $data = [
            'opinion' => 'Updated Opinion'
        ];
        $this->setupTest($this->validParentId, 'validToken', $data, $this->validChildId);
        $this->opinion->expects($this->once())->method('update')
            ->with($this->validParentId, $this->validChildId, 'validToken', $data)
            ->willReturn(true);
        $this->view->expects($this->once())->method('send')
            ->with('200', ['message' => "Done, opinion updated successfully"]);
        $this->controller->update();
    }

    public function testDelete(): void
    {
        $this->setupTest($this->validParentId, 'validToken', [], $this->validChildId);
        $this->opinion->expects($this->once())->method('destroy')
            ->with($this->validParentId, $this->validChildId, 'validToken')
            ->willReturn(true);
        $this->view->expects($this->once())->method('send')
            ->with('200', ['message' => "Done, opinion deleted successfully"]);
        $this->controller->delete();
    }
}
