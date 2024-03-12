<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\Controllers\HomeController;
use App\Views\HomeView;

class HomeControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $view = new HomeView();
        ob_start();
        $controller = new HomeController($view);
        $controller->index();
        $output = ob_get_clean();

        $expectedOutput = file_get_contents(__DIR__ . '/../../www/index.html');
        $this->assertEquals(explode(PHP_EOL, $expectedOutput), explode(PHP_EOL, $output));
    }
}
