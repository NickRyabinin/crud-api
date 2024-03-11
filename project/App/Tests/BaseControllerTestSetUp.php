<?php

namespace App\Tests;

use App\Core\Helper;
use App\Views\View;
use PHPUnit\Framework\TestCase;

class BaseControllerTestSetUp extends TestCase
{
    protected Helper $helper;
    protected View $view;

    protected function setUp(): void
    {
        $this->view = $this->createMock(View::class);
        $this->helper = $this->createMock(Helper::class);
    }

    protected function setupTest(
        string $id,
        string $token = '',
        array $inputData = [],
        string | bool $childId = ''
    ): void {
        $this->helper->method('getId')->willReturnMap([
            ['', $id],
            ['nested', $childId],
        ]);
        $this->helper->method('getToken')->willReturn($token);
        $this->helper->method('getInputData')->willReturn($inputData);
        $this->helper->method('sanitize')->willReturnArgument(0);
        $this->helper->method('validate')->willReturnArgument(0);
    }
}
