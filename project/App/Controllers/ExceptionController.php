<?php

/**
 * Класс ExceptionController.
 * Пока его единственное назначение - обрабатывать исключение, которое
 * бросает Роутер в случае, если не может сопоставить ни один контроллер
 * запрашиваемому клиентом ресурсу.
 */

namespace App\Controllers;

use App\Views\View;

class ExceptionController
{
    private View $view;

    public function __construct(View $view)
    {
        $this->view = $view;
    }

    public function handleException(\Exception $e): void
    {
        $responseCode = $e->getCode();
        $message = [
            'error' => "{$e->getMessage()}"
        ];
        $this->view->send($responseCode, $message);
    }
}
