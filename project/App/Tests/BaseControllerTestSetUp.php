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

    protected function setupTest(string $id, $token = null, $inputData = null): void
    {
        $this->helper->method('getId')->willReturn($id);
        $this->helper->method('getToken')->willReturn($token);
        $this->helper->method('getInputData')->willReturn($inputData);
        $this->helper->method('sanitize')->willReturnArgument(0);
        $this->helper->method('validate')->willReturnArgument(0);
    }
}