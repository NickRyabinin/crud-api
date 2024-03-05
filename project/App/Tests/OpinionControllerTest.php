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
        $this->opinion->belongsTo = ['book'];
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
}
