<?php

/**
 * Класс HomeController - контроллер "домашней страницы", имеет единственный
 * метод index(), вызывающий на View рендер страницы index.html.
 */

namespace App\Controllers;

use App\Views\HomeView;

class HomeController
{
    private $view;

    public function __construct(HomeView $view)
    {
        $this->view = $view;
    }

    public function index(): void
    {
        $this->view->render();
    }
}
