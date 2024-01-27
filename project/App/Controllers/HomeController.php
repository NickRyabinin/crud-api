<?php

namespace App\Controllers;

use App\Views\HomeView;

class HomeController
{
    private $view;

    public function __construct(HomeView $view)
    {
        $this->view = $view;
    }

    public function index()
    {
        $this->view->render();
    }
}
